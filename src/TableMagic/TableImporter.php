<?php

namespace ChernegaSergiy\TableMagic;

use Exception;
use SimpleXMLElement;

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

    /**
     * Imports data from JSON format into a new table.
     *
     * @param  string  $data  The JSON data to import.
     * @return Table The newly created table with imported data.
     * @throws Exception
     */
    protected function fromJson(string $data) : Table
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

    /**
     * Imports data from XML format into a new table.
     *
     * @param  string  $data  The XML data to import.
     * @return Table The newly created table with imported data.
     * @throws Exception
     */
    protected function fromXml(string $data) : Table
    {
        $xml = simplexml_load_string($data);
        if (false === $xml) {
            throw new Exception('Invalid XML data');
        }

        $headers = [];
        if (isset($xml->headers->header)) {
            foreach ($xml->headers->header as $header) {
                $headers[] = (string) $header;
            }
        }

        $table = new Table($headers);

        if (isset($xml->rows->row)) {
            foreach ($xml->rows->row as $row) {
                $new_row = [];
                foreach ($headers as $header) {
                    $new_row[] = isset($row->{$header}) ? (string) $row->{$header} : '';
                }
                $table->addRow($new_row);
            }
        }

        return $table;
    }
}
