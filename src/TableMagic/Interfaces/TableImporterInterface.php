<?php

namespace ChernegaSergiy\TableMagic\Interfaces;

use ChernegaSergiy\TableMagic\Table;

interface TableImporterInterface
{
    /**
     * Imports data from a string into a Table object.
     *
     * @param  string  $data  The data string to import.
     * @return Table The imported Table object.
     *
     * @throws \Exception If the import fails.
     */
    public function import(string $data) : Table;
}
