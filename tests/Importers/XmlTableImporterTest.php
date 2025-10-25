<?php

namespace Tests\Importers;

use ChernegaSergiy\TableMagic\Importers\XmlTableImporter;
use ChernegaSergiy\TableMagic\Table;
use Exception;
use PHPUnit\Framework\TestCase;

class XmlTableImporterTest extends TestCase
{
    public function testImportFromXml()
    {
        $importer = new XmlTableImporter();
        $xml = '<root><headers><header>Name</header><header>Age</header></headers><rows><row><Name>Alice</Name><Age>30</Age></row><row><Name>Bob</Name><Age>25</Age></row></rows></root>';
        $table = $importer->import($xml);
        $this->assertInstanceOf(Table::class, $table);
        $this->assertEquals(['Name', 'Age'], $table->headers);
        $this->assertEquals([['Alice', '30'], ['Bob', '25']], $table->getRows());
    }

    public function testImportWithInvalidXml()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Invalid XML data');
        $importer = new XmlTableImporter();
        $importer->import('invalid-xml');
    }

    public function testImportFromXmlWithMissingCell()
    {
        $importer = new XmlTableImporter();
        $xml = '<root><headers><header>Name</header><header>Age</header></headers><rows><row><Name>Alice</Name></row></rows></root>';
        $table = $importer->import($xml);
        $this->assertInstanceOf(Table::class, $table);
        $this->assertEquals(['Name', 'Age'], $table->headers);
        $this->assertEquals([['Alice', '']], $table->getRows());
    }
}
