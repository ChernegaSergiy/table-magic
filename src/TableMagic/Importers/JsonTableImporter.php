<?php

namespace ChernegaSergiy\TableMagic\Importers;

use ChernegaSergiy\TableMagic\Interfaces\TableImporterInterface;
use ChernegaSergiy\TableMagic\Table;
use Exception;

class JsonTableImporter implements TableImporterInterface
{
    /**
     * Imports data from JSON format into a new table.
     *
     * @param  string  $data  The JSON data to import.
     * @return Table The newly created table with imported data.
     *
     * @throws Exception If an error occurs during JSON import.
     */
    public function import(string $data) : Table
    {
        $decoded = json_decode($data, true);
        if (! is_array($decoded)) {
            throw new Exception('Invalid JSON data');
        }
        /** @var array<int, string> $headers */
        $headers = $decoded['headers'] ?? [];
        $table = new Table($headers);

        /** @var array<int, array<int, string>> $rows */
        $rows = $decoded['rows'] ?? [];
        foreach ($rows as $row) {
            $table->addRow($row);
        }

        return $table;
    }
}
