<?php

namespace ChernegaSergiy\TableMagic\Interfaces;

use ChernegaSergiy\TableMagic\Table;

interface TableExporterInterface
{
    /**
     * Exports a Table object to a string representation.
     *
     * @param Table $table The Table object to export.
     * @return string The string representation of the table.
     * @throws \Exception If the export fails.
     */
    public function export(Table $table): string;
}
