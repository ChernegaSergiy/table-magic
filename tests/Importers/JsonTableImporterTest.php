<?php

namespace Tests\Importers;

use ChernegaSergiy\TableMagic\Importers\JsonTableImporter;
use ChernegaSergiy\TableMagic\Table;
use Exception;
use PHPUnit\Framework\TestCase;

class JsonTableImporterTest extends TestCase
{
    public function testImportFromJson()
    {
        $importer = new JsonTableImporter();
        $json = '{"headers":["Name","Age"],"rows":[["Alice",30],["Bob",25]]}';
        $table = $importer->import($json);
        $this->assertInstanceOf(Table::class, $table);
        $this->assertEquals(['Name', 'Age'], $table->headers);
        $this->assertEquals([['Alice', 30], ['Bob', 25]], $table->getRows());
    }

    public function testImportWithInvalidJson()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Invalid JSON data');
        $importer = new JsonTableImporter();
        $importer->import('invalid-json');
    }
}
