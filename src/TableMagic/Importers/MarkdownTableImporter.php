<?php

namespace ChernegaSergiy\TableMagic\Importers;

use ChernegaSergiy\TableMagic\Interfaces\TableImporterInterface;
use ChernegaSergiy\TableMagic\Table;

class MarkdownTableImporter implements TableImporterInterface
{
    /**
     * Imports data from a Markdown table string into a Table object.
     *
     * @param  string $data The Markdown table string to import.
     * @return Table  The imported Table object.
     */
    public function import(string $data) : Table
    {
        $lines = array_map('trim', explode("\n", trim($data)));

        // Extract headers
        $header_line = array_shift($lines);
        $headers = $this->parseRow($header_line);

        // Extract alignments from separator line
        $separator_line = array_shift($lines);
        $alignments = [];
        if (null !== $separator_line) {
            $alignments = $this->parseAlignment($separator_line, $headers);
        }

        $table = new Table($headers, $alignments);

        // Extract rows
        foreach ($lines as $line) {
            if (trim($line)) {
                $table->addRow($this->parseRow($line));
            }
        }

        return $table;
    }

    /**
     * @param  string             $line
     * @return array<int, string>
     */
    private function parseRow(string $line) : array
    {
        $parts = explode('|', trim($line, '| '));
        return array_map('trim', $parts);
    }

    /**
     * @param  string                $line
     * @param  array<int, string>    $headers
     * @return array<string, string>
     */
    private function parseAlignment(string $line, array $headers) : array
    {
        $alignments = [];
        $columns = explode('|', trim($line, '| '));

        foreach ($columns as $index => $column) {
            $column = trim($column);
            $header = $headers[$index] ?? null;
            if (null === $header) {
                continue;
            }

            $first_char = $column[0];
            $last_char = $column[strlen($column) - 1];

            if (':' === $first_char && ':' === $last_char) {
                $alignments[$header] = 'c';
            } elseif (':' === $last_char) {
                $alignments[$header] = 'r';
            } else {
                $alignments[$header] = 'l';
            }
        }

        return $alignments;
    }
}
