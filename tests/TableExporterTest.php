<?php

namespace Tests;

use ChernegaSergiy\TableMagic\Exporters\CsvTableExporter;
use ChernegaSergiy\TableMagic\Exporters\HtmlTableExporter;
use ChernegaSergiy\TableMagic\Exporters\JsonTableExporter;
use ChernegaSergiy\TableMagic\Exporters\XmlTableExporter;
use ChernegaSergiy\TableMagic\Table;
use ChernegaSergiy\TableMagic\TableExporter;
use Exception;
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

    public function testExportFactoryReturnsCorrectExporterAndExportsData()
    {
        $exporter = new TableExporter($this->table);

        // Test HTML
        $expectedHtml = '<table border="1"><thead><tr><th>Name</th><th>Age</th></tr></thead><tbody><tr><td>Alice</td><td>30</td></tr><tr><td>Bob</td><td>25</td></tr></tbody></table>';
        $this->assertEquals($expectedHtml, $exporter->export('html'));

        // Test CSV
        $expectedCsv = "Name,Age\nAlice,30\nBob,25\n";
        $this->assertEquals($expectedCsv, $exporter->export('csv'));

        // Test JSON
        $expectedJson = '{"headers":["Name","Age"],"rows":[["Alice",30],["Bob",25]]}';
        $this->assertEquals($expectedJson, $exporter->export('json'));

        // Test XML
        $expectedXml = '<?xml version="1.0"?><table><headers><header>Name</header><header>Age</header></headers><rows><row><Name>Alice</Name><Age>30</Age></row><row><Name>Bob</Name><Age>25</Age></row></rows></table>';
        $this->assertXmlStringEqualsXmlString($expectedXml, $exporter->export('xml'));

        // Test Markdown
        $expectedMarkdown = "| Name | Age |\n|:---|:---|\n| Alice | 30 |\n| Bob | 25 |";
        $this->assertEquals($expectedMarkdown, $exporter->export('markdown'));
    }

    public function testExportWithUnsupportedFormatThrowsException()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Unsupported format: yml');
        $exporter = new TableExporter($this->table);
        $exporter->export('yml');
    }
}