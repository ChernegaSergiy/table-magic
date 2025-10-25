<?php

namespace Tests\Exporters;

use ChernegaSergiy\TableMagic\Exporters\CsvTableExporter;
use ChernegaSergiy\TableMagic\Table;
use Exception;
use PHPUnit\Framework\TestCase;

class CsvTableExporterTest extends TestCase
{
    private Table $table;

    protected function setUp() : void
    {
        parent::setUp();
        $this->table = new Table(['Name', 'Age']);
        $this->table->addRow(['Alice', 30]);
        $this->table->addRow(['Bob', 25]);
    }

    public function testExportToCsv()
    {
        $exporter = new CsvTableExporter();
        $expectedCsv = "Name,Age\nAlice,30\nBob,25\n";
        $this->assertEquals($expectedCsv, $exporter->export($this->table));
    }

    public function testExportEmptyTable()
    {
        $emptyTable = new Table();
        $exporter = new CsvTableExporter();
        $expectedCsv = "\n";
        $this->assertEquals($expectedCsv, $exporter->export($emptyTable));
    }

    public function testExportWithEmptyRowsAndValues()
    {
        $table = new Table(['Col1', 'Col2']);
        $table->addRow(['Val1', '']); // Row with empty value
        $table->addRow([]); // Empty row
        $table->addRow(['', 'Val2']); // Row with empty value

        $exporter = new CsvTableExporter();

        $expectedCsv = "Col1,Col2\nVal1,\n,\n,Val2\n";
        $this->assertEquals($expectedCsv, $exporter->export($table));
    }

    public function testToCsvFailsToOpenStream()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Failed to open temporary stream');

        $table = new Table(['Header']);
        $exporter = new class() extends CsvTableExporter {
            protected function openTemporaryStream()
            {
                return false;
            }
        };
        $exporter->export($table);
    }
}
