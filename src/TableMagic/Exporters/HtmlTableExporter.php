<?php

namespace ChernegaSergiy\TableMagic\Exporters;

use ChernegaSergiy\TableMagic\Interfaces\TableExporterInterface;
use ChernegaSergiy\TableMagic\Table;

class HtmlTableExporter implements TableExporterInterface
{
    /**
     * Exports a Table object to an HTML string representation.
     *
     * @param  Table  $table  The Table object to export.
     * @return string The HTML string representation of the table.
     */
    public function export(Table $table) : string
    {
        $html = '<table border="1"><thead><tr>';
        foreach ($table->headers as $header) {
            $html .= "<th>{$header}</th>";
        }
        $html .= '</tr></thead><tbody>';
        foreach ($table->getRows() as $row) {
            $html .= '<tr>';
            foreach ($row as $cell) {
                $html .= "<td>{$cell}</td>";
            }
            $html .= '</tr>';
        }
        $html .= '</tbody></table>';

        return $html;
    }
}
