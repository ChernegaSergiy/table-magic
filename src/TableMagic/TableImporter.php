<?php

namespace ChernegaSergiy\TableMagic;

use ChernegaSergiy\TableMagic\Importers\CsvTableImporter;
use ChernegaSergiy\TableMagic\Importers\JsonTableImporter;
use ChernegaSergiy\TableMagic\Importers\XmlTableImporter;
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
                $importer = new CsvTableImporter();
                break;
            case 'json':
                $importer = new JsonTableImporter();
                break;
            case 'xml':
                $importer = new XmlTableImporter();
                break;
            default:
                throw new Exception("Unsupported format: $format");
        }

        return $importer->import($data);
    }
}