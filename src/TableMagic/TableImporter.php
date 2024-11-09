<?php

namespace ChernegaSergiy\TableMagic;

use Exception;

class TableImporter
{
    /**
     * Imports data into a new table from the specified format.
     *
     * @param  string  $data  The data to import.
     * @param  string  $format  The format of the data ('csv', 'json', 'xml').
     * @return Table The newly created table with imported data.
     *
     * @throws Exception If the format is unsupported.
     */
    public function import(string $data, string $format) : Table
    {
        switch (strtolower($format)) {
            case 'csv':
                return $this->fromCsv($data);
            case 'json':
                return $this->fromJson($data);
            case 'xml':
                return $this->fromXml($data);
            default:
                throw new Exception("Unsupported format: $format");
        }
    }

    /**
     * Imports data from CSV format into a new table.
     *
     * @param  string  $data  The CSV data to import.
     * @return Table The newly created table with imported data.
     */
    protected function fromCsv(string $data) : Table
    {
        $lines = explode("\n", $data);
        $headers = str_getcsv(array_shift($lines));
        $table = new Table($headers);

        foreach ($lines as $line) {
            if (! empty(trim($line))) {
                $table->addRow(str_getcsv($line));
            }
        }

        return $table;
    }

    /**
     * Imports data from JSON format into a new table.
     *
     * @param  string  $data  The JSON data to import.
     * @return Table The newly created table with imported data.
     */
    protected function fromJson(string $data) : Table
    {
        $decoded = json_decode($data, true);
        $table = new Table($decoded['headers'] ?? []);
        $table->rows = $decoded['rows'] ?? [];

        return $table;
    }

    /**
     * Imports data from XML format into a new table.
     *
     * @param  string  $data  The XML data to import.
     * @return Table The newly created table with imported data.
     */
    protected function fromXml(string $data) : Table
    {
        $xml = simplexml_load_string($data);
        $headers = explode(',', (string) $xml->headers);
        $table = new Table($headers);

        foreach ($xml->row as $row) {
            $newRow = [];
            foreach ($row->cell as $cell) {
                $newRow[] = (string) $cell;
            }
            $table->addRow($newRow);
        }

        return $table;
    }
}
