<?php

namespace Tests\Exporters;

use ChernegaSergiy\TableMagic\Exporters\JsonTableExporter;
use ChernegaSergiy\TableMagic\Table;
use Exception;
use PHPUnit\Framework\TestCase;

class JsonTableExporterTest extends TestCase
{
    private Table $table;

    protected function setUp() : void
    {
        parent::setUp();
        $this->table = new Table(['Name', 'Age']);
        $this->table->addRow(['Alice', 30]);
        $this->table->addRow(['Bob', 25]);
    }

    public function testExportToJson()
    {
        $exporter = new JsonTableExporter();
        $expectedJson = '{"headers":["Name","Age"],"rows":[["Alice",30],["Bob",25]]}';
        $this->assertEquals($expectedJson, $exporter->export($this->table));
    }

    public function testExportEmptyTable()
    {
        $emptyTable = new Table();
        $exporter = new JsonTableExporter();
        $expectedJson = '{"headers":[],"rows":[]}';
        $this->assertEquals($expectedJson, $exporter->export($emptyTable));
    }

    public function testExportWithEmptyRowsAndValues()
    {
        $table = new Table(['Col1', 'Col2']);
        $table->addRow(['Val1', '']); // Row with empty value
        $table->addRow([]); // Empty row
        $table->addRow(['', 'Val2']); // Row with empty value

        $exporter = new JsonTableExporter();

        $expectedJson = '{"headers":["Col1","Col2"],"rows":[["Val1",""],["",""],["","Val2"]]}';
        $this->assertEquals($expectedJson, $exporter->export($table));
    }

    public function testExportToJsonWithInvalidUtf8Data(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Failed to encode data to JSON');

        $table = new Table(['Col1']);
        // Add a row with an explicitly invalid UTF-8 sequence
        $table->addRow(["invalid\x80utf8"]);

        $exporter = new JsonTableExporter();
        $exporter->export($table);
    }
}
