<?php

namespace ChernegaSergiy\TableMagic;

use ChernegaSergiy\TableMagic\Exporters\CsvTableExporter;
use ChernegaSergiy\TableMagic\Exporters\HtmlTableExporter;
use ChernegaSergiy\TableMagic\Exporters\JsonTableExporter;
use ChernegaSergiy\TableMagic\Exporters\MarkdownTableExporter;
use ChernegaSergiy\TableMagic\Exporters\XmlTableExporter;
use Exception;

class TableExporter
{
    private Table $table;

    /**
     * TableExporter constructor.
     *
     * @param  Table  $table  The table to be exported.
     */
    public function __construct(Table $table)
    {
        $this->table = $table;
    }

    /**
     * Exports the table in the specified format.
     *
     * @param  string  $format  The format to export the table ('html', 'csv', 'json', 'xml').
     * @return string The exported table data as a string.
     *
     * @throws Exception If the format is unsupported.
     */
    public function export(string $format) : string
    {
        switch (strtolower($format)) {
            case 'html':
                $exporter = new HtmlTableExporter();
                break;
            case 'csv':
                $exporter = new CsvTableExporter();
                break;
            case 'json':
                $exporter = new JsonTableExporter();
                break;
            case 'markdown':
                $exporter = new MarkdownTableExporter();
                break;
            case 'xml':
                $exporter = new XmlTableExporter();
                break;
            default:
                throw new Exception("Unsupported format: $format");
        }

        return $exporter->export($this->table);
    }
}