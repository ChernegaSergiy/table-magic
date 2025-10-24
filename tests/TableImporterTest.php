<?php

use ChernegaSergiy\TableMagic\Table;
use ChernegaSergiy\TableMagic\TableImporter;
use PHPUnit\Framework\TestCase;

class TableImporterTest extends TestCase
{
    public function testImportFromCsv()
    {
        $importer = new TableImporter();
        $csv = "Name,Age\nAlice,30\nBob,25";
        $table = $importer->import($csv, 'csv');
        $this->assertInstanceOf(Table::class, $table);
        $this->assertEquals(['Name', 'Age'], $table->headers);
        $this->assertEquals([['Alice', '30'], ['Bob', '25']], $table->rows);
    }

    public function testImportFromJson()
    {
        $importer = new TableImporter();
        $json = '{"headers":["Name","Age"],"rows":[["Alice",30],["Bob",25]]}';
        $table = $importer->import($json, 'json');
        $this->assertInstanceOf(Table::class, $table);
        $this->assertEquals(['Name', 'Age'], $table->headers);
        $this->assertEquals([['Alice', 30], ['Bob', 25]], $table->rows);
    }

    public function testImportFromXml()
    {
        $importer = new TableImporter();
        $xml = '<root><headers><header>Name</header><header>Age</header></headers><rows><row><Name>Alice</Name><Age>30</Age></row><row><Name>Bob</Name><Age>25</Age></row></rows></root>';
        $table = $importer->import($xml, 'xml');
        $this->assertInstanceOf(Table::class, $table);
        $this->assertEquals(['Name', 'Age'], $table->headers);
        $this->assertEquals([['Alice', '30'], ['Bob', '25']], $table->rows);
    }

    public function testImportWithUnsupportedFormat()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Unsupported format: yml');
        $importer = new TableImporter();
        $importer->import('', 'yml');
    }

    public function testImportWithInvalidJson()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Invalid JSON data');
        $importer = new TableImporter();
        $importer->import('invalid-json', 'json');
    }

    public function testImportWithInvalidXml()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Invalid XML data');
        $importer = new TableImporter();
        $importer->import('invalid-xml', 'xml');
    }
}
