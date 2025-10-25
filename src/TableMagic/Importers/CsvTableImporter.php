<?php

namespace ChernegaSergiy\TableMagic\Importers;

use ChernegaSergiy\TableMagic\Interfaces\TableImporterInterface;
use ChernegaSergiy\TableMagic\Table;

class CsvTableImporter implements TableImporterInterface
{
    /**
     * Imports data from CSV format into a new table.
     *
     * @param  string  $data  The CSV data to import.
     * @return Table The newly created table with imported data.
     */
    public function import(string $data) : Table
    {
        $lines = explode("\n", trim($data));
        $raw_headers = str_getcsv((string) array_shift($lines));
        $headers = array_map(fn($header) => $header ?? '', $raw_headers);
        $table = new Table($headers);

        foreach ($lines as $line) {
            if (! empty(trim($line))) {
                $row = str_getcsv($line);
                $table->addRow(array_map(fn($cell) => $cell ?? '', $row));
            }
        }

        return $table;
    }
}
