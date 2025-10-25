<?php

namespace ChernegaSergiy\TableMagic\Exporters;

use ChernegaSergiy\TableMagic\Interfaces\TableExporterInterface;
use ChernegaSergiy\TableMagic\Table;

class MarkdownTableExporter implements TableExporterInterface
{
    /**
     * Exports a Table object to a Markdown string representation.
     *
     * @param  Table  $table  The Table object to export.
     * @return string The Markdown string representation of the table.
     */
    public function export(Table $table) : string
    {
        $lines = [];

        // Headers
        $lines[] = '| ' . implode(' | ', $table->headers) . ' |';

        // Separator
        $separator = [];
        $alignments = $table->getAlignments();

        foreach ($table->headers as $index => $header) {
            $align = $alignments[$index] ?? 'l';

            switch ($align) {
                case 'c':
                    $separator[] = ':---:';
                    break;
                case 'r':
                    $separator[] = '---:';
                    break;
                case 'l':
                default:
                    $separator[] = ':---';
                    break;
            }
        }
        $lines[] = '|' . implode('|', $separator) . '|';

        // Rows
        foreach ($table->getRows() as $row) {
            $lines[] = '| ' . implode(' | ', $row) . ' |';
        }

        return implode("\n", $lines);
    }
}
