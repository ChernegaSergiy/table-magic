<?php

namespace Tests\Importers;

use ChernegaSergiy\TableMagic\Importers\CsvTableImporter;
use ChernegaSergiy\TableMagic\Table;
use PHPUnit\Framework\TestCase;

class CsvTableImporterTest extends TestCase
{
    public function testImportFromCsv()
    {
        $importer = new CsvTableImporter();
        $csv = "Name,Age\nAlice,30\nBob,25";
        $table = $importer->import($csv);
        $this->assertInstanceOf(Table::class, $table);
        $this->assertEquals(['Name', 'Age'], $table->headers);
        $this->assertEquals([['Alice', '30'], ['Bob', '25']], $table->getRows());
    }
}
