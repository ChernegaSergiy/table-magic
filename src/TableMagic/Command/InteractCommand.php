<?php

namespace ChernegaSergiy\TableMagic\Command;

use ChernegaSergiy\TableMagic\TableImporter;
use ChernegaSergiy\TableMagic\TerminalInteraction;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class InteractCommand extends Command
{
    protected function configure() : void
    {
        $this
            ->setName('interact')
            ->setDescription('Opens a table in interactive mode (pagination, editing, sorting)')
            ->addArgument('file', InputArgument::REQUIRED, 'The input file containing the data')
            ->addOption('format', 'f', InputOption::VALUE_OPTIONAL, 'The format of the input data (csv, json, xml, markdown). Guessed from extension if not provided.')
            ->addOption('style', 's', InputOption::VALUE_OPTIONAL, 'The style to apply to the table', 'default')
            ->addOption('rows', 'r', InputOption::VALUE_OPTIONAL, 'Number of rows to display per page', 5)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $file = $input->getArgument('file');
        if (! is_string($file)) {
            $output->writeln('<error>Invalid file argument.</error>');
            return Command::FAILURE;
        }

        if (! file_exists($file) || ! is_readable($file)) {
            $output->writeln("<error>File not found or not readable: {$file}</error>");
            return Command::FAILURE;
        }

        $format = $input->getOption('format');
        if (! is_string($format) || '' === $format) {
            $format = pathinfo($file, PATHINFO_EXTENSION);
            if ('' === $format) {
                $output->writeln('<error>Could not guess format from file extension. Please specify using --format.</error>');
                return Command::FAILURE;
            }
        }

        $data = file_get_contents($file);
        if (false === $data) {
            $output->writeln("<error>Failed to read file: {$file}</error>");
            return Command::FAILURE;
        }

        try {
            $importer = new TableImporter();
            $table = $importer->import($data, strtolower($format));

            $style = $input->getOption('style');
            if (is_string($style) && '' !== $style) {
                $table->setStyle($style);
            }

            $rows_option = $input->getOption('rows');
            $rows_per_page = is_numeric($rows_option) ? (int) $rows_option : 5;

            $output->writeln("<info>Starting interactive mode for {$file}...</info>");

            $inputStream = $input->getStream() ?: STDIN;
            $outputStream = $output instanceof \Symfony\Component\Console\Output\StreamOutput ? $output->getStream() : STDOUT;

            $interaction = new TerminalInteraction($table, $rows_per_page, $inputStream, $outputStream);
            $interaction->run();

            $output->writeln("\n<info>Interactive mode exited.</info>");

            return Command::SUCCESS;
        } catch (Exception $e) {
            $output->writeln('<error>Error: ' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }
    }
}
