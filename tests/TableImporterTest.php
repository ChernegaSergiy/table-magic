<?php

namespace Tests;

use ChernegaSergiy\TableMagic\Importers\CsvTableImporter;
use ChernegaSergiy\TableMagic\Importers\JsonTableImporter;
use ChernegaSergiy\TableMagic\Importers\XmlTableImporter;
use ChernegaSergiy\TableMagic\Table;
use ChernegaSergiy\TableMagic\TableImporter;
use Exception;
use PHPUnit\Framework\TestCase;

class TableImporterTest extends TestCase
{
    public function testImportFactoryReturnsCorrectImporterAndImportsData()
    {
        $importer = new TableImporter();

        // Test CSV
        $csv_data = "Name,Age\nAlice,30";
        $table_csv = $importer->import($csv_data, 'csv');
        $this->assertInstanceOf(Table::class, $table_csv);
        $this->assertEquals(['Name', 'Age'], $table_csv->headers);
        $this->assertEquals([['Alice', '30']], $table_csv->getRows());

        // Test JSON
        $json_data = '{"headers":["Name","Age"],"rows":[["Bob",25]]}';
        $table_json = $importer->import($json_data, 'json');
        $this->assertInstanceOf(Table::class, $table_json);
        $this->assertEquals(['Name', 'Age'], $table_json->headers);
        $this->assertEquals([['Bob', 25]], $table_json->getRows());

        // Test XML
        $xml_data = '<root><headers><header>Name</header><header>Age</header></headers><rows><row><Name>Charlie</Name><Age>35</Age></row></rows></root>';
        $table_xml = $importer->import($xml_data, 'xml');
        $this->assertInstanceOf(Table::class, $table_xml);
        $this->assertEquals(['Name', 'Age'], $table_xml->headers);
        $this->assertEquals([['Charlie', '35']], $table_xml->getRows());

        // Test Markdown
        $markdown_data = "| Name | Age |\n|:---|:---|
| David | 40 |";
        $table_markdown = $importer->import($markdown_data, 'markdown');
        $this->assertInstanceOf(Table::class, $table_markdown);
        $this->assertEquals(['Name', 'Age'], $table_markdown->headers);
        $this->assertEquals([['David', '40']], $table_markdown->getRows());
    }

    public function testImportWithUnsupportedFormatThrowsException()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Unsupported format: yml');
        $importer = new TableImporter();
        $importer->import('', 'yml');
    }
}