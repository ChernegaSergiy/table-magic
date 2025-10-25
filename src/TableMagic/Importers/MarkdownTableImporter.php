<?php

namespace ChernegaSergiy\TableMagic\Importers;

use ChernegaSergiy\TableMagic\Interfaces\TableImporterInterface;
use ChernegaSergiy\TableMagic\Table;

class MarkdownTableImporter implements TableImporterInterface
{
    /**
     * Imports data from a Markdown table string into a Table object.
     *
     * @param  string  $data  The Markdown table string to import.
     * @return Table The imported Table object.
     */
    public function import(string $data) : Table
    {
        $lines = array_map('trim', explode("\n", trim($data)));

        // Extract headers
        $headerLine = array_shift($lines);
        $headers = $this->parseRow($headerLine);

        // Extract alignments from separator line
        $separatorLine = array_shift($lines);
        $alignments = [];
        if ($separatorLine !== null) {
            $alignments = $this->parseAlignment($separatorLine, $headers);
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
     * @param  string  $line
     * @return array<int, string>
     */
    private function parseRow(string $line) : array
    {
        $parts = explode('|', trim($line, '| '));
        return array_map('trim', $parts);
    }

    /**
     * @param  string  $line
     * @param  array<int, string>  $headers
     * @return array<string, string>
     */
    private function parseAlignment(string $line, array $headers) : array
    {
        $alignments = [];
        $columns = explode('|', trim($line, '| '));

        foreach ($columns as $index => $column) {
            $column = trim($column);
            $header = $headers[$index] ?? null;
            if ($header === null) continue;

            $firstChar = $column[0];
            $lastChar = $column[strlen($column) - 1];

            if ($firstChar === ':' && $lastChar === ':') {
                $alignments[$header] = 'c';
            } elseif ($lastChar === ':') {
                $alignments[$header] = 'r';
            } else {
                $alignments[$header] = 'l';
            }
        }

        return $alignments;
    }
}
