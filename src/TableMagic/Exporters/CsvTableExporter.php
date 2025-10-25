<?php

namespace ChernegaSergiy\TableMagic\Exporters;

use ChernegaSergiy\TableMagic\Interfaces\TableExporterInterface;
use ChernegaSergiy\TableMagic\Table;
use Exception;

class CsvTableExporter implements TableExporterInterface
{
    /**
     * Exports a Table object to a CSV string representation.
     *
     * @param Table $table The Table object to export.
     * @return string The CSV string representation of the table.
     *
     * @throws Exception If an error occurs during CSV conversion.
     */
    public function export(Table $table): string
    {
        $output = $this->openTemporaryStream();
        if (false === $output) {
            throw new Exception('Failed to open temporary stream');
        }
        fputcsv($output, $table->headers);
        foreach ($table->getRows() as $row) {
            fputcsv($output, $row);
        }
        rewind($output);
        $csv_data = stream_get_contents($output);
        fclose($output);

        return (string) $csv_data;
    }

    /**
     * @return resource|false
     */
    protected function openTemporaryStream()
    {
        return fopen('php://temp', 'r+');
    }
}
