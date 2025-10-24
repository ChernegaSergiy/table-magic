<?php

use ChernegaSergiy\TableMagic\Table;
use ChernegaSergiy\TableMagic\TableExporter;
use PHPUnit\Framework\TestCase;

class TableExporterTest extends TestCase
{
    private Table $table;

    protected function setUp() : void
    {
        parent::setUp();
        $this->table = new Table(['Name', 'Age']);
        $this->table->addRow(['Alice', 30]);
        $this->table->addRow(['Bob', 25]);
    }

    public function testExportToHtml()
    {
        $exporter = new TableExporter($this->table);
        $expectedHtml = '<table border="1"><thead><tr><th>Name</th><th>Age</th></tr></thead><tbody><tr><td>Alice</td><td>30</td></tr><tr><td>Bob</td><td>25</td></tr></tbody></table>';
        $this->assertEquals($expectedHtml, $exporter->export('html'));
    }

    public function testExportToCsv()
    {
        $exporter = new TableExporter($this->table);
        $expectedCsv = "Name,Age\nAlice,30\nBob,25\n";
        $this->assertEquals($expectedCsv, $exporter->export('csv'));
    }

    public function testExportToJson()
    {
        $exporter = new TableExporter($this->table);
        $expectedJson = '{"headers":["Name","Age"],"rows":[["Alice",30],["Bob",25]]}';
        $this->assertEquals($expectedJson, $exporter->export('json'));
    }

    public function testExportToXml()
    {
        $exporter = new TableExporter($this->table);
        $expectedXml = '<?xml version="1.0"?><table><headers><header>Name</header><header>Age</header></headers><rows><row><Name>Alice</Name><Age>30</Age></row><row><Name>Bob</Name><Age>25</Age></row></rows></table>';
        $this->assertXmlStringEqualsXmlString($expectedXml, $exporter->export('xml'));
    }

    public function testExportWithUnsupportedFormat()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Unsupported format: yml');
        $exporter = new TableExporter($this->table);
        $exporter->export('yml');
    }

    public function testExportEmptyTable()
    {
        $emptyTable = new Table();
        $exporter = new TableExporter($emptyTable);

        // HTML
        $expectedHtml = '<table border="1"><thead><tr></tr></thead><tbody></tbody></table>';
        $this->assertEquals($expectedHtml, $exporter->export('html'));

        // CSV
        $expectedCsv = "\n";
        $this->assertEquals($expectedCsv, $exporter->export('csv'));

        // JSON
        $expectedJson = '{"headers":[],"rows":[]}';
        $this->assertEquals($expectedJson, $exporter->export('json'));

        // XML
        $expectedXml = '<?xml version="1.0"?><table><headers/><rows/></table>';
        $this->assertXmlStringEqualsXmlString($expectedXml, $exporter->export('xml'));
    }
}
