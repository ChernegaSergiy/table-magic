<?php

namespace ChernegaSergiy\TableMagic;

use Exception;

class TableImporter
{
    private Table $table;

    /**
     * TableImporter constructor.
     *
     * @param  Table  $table  The table to import data into.
     */
    public function __construct(Table $table)
    {
        $this->table = $table;
    }

    /**
     * Imports data into the table from the specified format.
     *
     * @param  string  $data  The data to import.
     * @param  string  $format  The format of the data ('csv', 'json', 'xml').
     *
     * @throws Exception If the format is unsupported.
     */
    public function import(string $data, string $format) : void
    {
        switch (strtolower($format)) {
            case 'csv':
                $this->fromCsv($data);
                break;
            case 'json':
                $this->fromJson($data);
                break;
            case 'xml':
                $this->fromXml($data);
                break;
            default:
                throw new Exception("Unsupported format: $format");
        }
    }

    /**
     * Imports data from CSV format into the table.
     *
     * @param  string  $data  The CSV data to import.
     */
    protected function fromCsv(string $data) : void
    {
        $lines = explode("\n", $data);
        $this->table->headers = str_getcsv(array_shift($lines));
        foreach ($lines as $line) {
            if (! empty(trim($line))) {
                $this->table->addRow(str_getcsv($line));
            }
        }
        $this->table->updateColWidths($this->table->headers);
    }

    /**
     * Imports data from JSON format into the table.
     *
     * @param  string  $data  The JSON data to import.
     */
    protected function fromJson(string $data) : void
    {
        $decoded = json_decode($data, true);
        $this->table->headers = $decoded['headers'] ?? [];
        $this->table->rows = $decoded['rows'] ?? [];
        $this->table->updateColWidths($this->table->headers);
        foreach ($this->table->rows as $row) {
            $this->table->updateColWidths($row);
        }
    }

    /**
     * Imports data from XML format into the table.
     *
     * @param  string  $data  The XML data to import.
     */
    protected function fromXml(string $data) : void
    {
        $xml = simplexml_load_string($data);
        $this->table->headers = explode(',', (string) $xml->headers);
        foreach ($xml->row as $row) {
            $newRow = [];
            foreach ($row->cell as $cell) {
                $newRow[] = (string) $cell;
            }
            $this->table->addRow($newRow);
        }
        $this->table->updateColWidths($this->table->headers);
        foreach ($this->table->rows as $row) {
            $this->table->updateColWidths($row);
        }
    }
}
