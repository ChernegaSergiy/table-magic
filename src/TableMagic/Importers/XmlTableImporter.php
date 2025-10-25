<?php

namespace ChernegaSergiy\TableMagic\Importers;

use ChernegaSergiy\TableMagic\Interfaces\TableImporterInterface;
use ChernegaSergiy\TableMagic\Table;
use Exception;
use SimpleXMLElement;

class XmlTableImporter implements TableImporterInterface
{
    /**
     * Imports data from XML format into a new table.
     *
     * @param  string  $data  The XML data to import.
     * @return Table The newly created table with imported data.
     *
     * @throws Exception If an error occurs during XML import.
     */
    public function import(string $data) : Table
    {
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($data);
        libxml_clear_errors();
        if (false === $xml) {
            throw new Exception('Invalid XML data');
        }

        $headers = [];
        if (isset($xml->headers->header)) {
            foreach ($xml->headers->header as $header_node) {
                /** @var SimpleXMLElement $header_node */
                $header_text = (string) $header_node;
                /** @var string $header_text */
                if (! empty($header_text)) {
                    $headers[] = $header_text;
                }
            }
        }

        $table = new Table($headers);

        if (isset($xml->rows->row)) {
            foreach ($xml->rows->row as $row_node) {
                $new_row = [];
                foreach ($headers as $header) {
                    if (isset($row_node->{$header})) {
                        /** @var SimpleXMLElement $cell_value */
                        $cell_value = $row_node->{$header};
                        $new_row[] = (string) $cell_value;
                    } else {
                        $new_row[] = '';
                    }
                }
                $table->addRow($new_row);
            }
        }

        return $table;
    }
}
