<?php

namespace ChernegaSergiy\TableMagic\Exporters;

use ChernegaSergiy\TableMagic\Interfaces\TableExporterInterface;
use ChernegaSergiy\TableMagic\Table;
use Exception;

class JsonTableExporter implements TableExporterInterface
{
    /**
     * Exports a Table object to a JSON string representation.
     *
     * @param  Table  $table  The Table object to export.
     * @return string The JSON string representation of the table.
     *
     * @throws Exception If an error occurs during JSON conversion.
     */
    public function export(Table $table) : string
    {
        $data = [
            'headers' => $table->headers,
            'rows' => $table->getRows(),
        ];

        $json = json_encode($data);
        if (false === $json) {
            throw new Exception('Failed to encode data to JSON');
        }

        return $json;
    }
}
