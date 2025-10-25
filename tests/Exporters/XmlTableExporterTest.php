<?php

namespace Tests\Exporters;

use ChernegaSergiy\TableMagic\Exporters\XmlTableExporter;
use ChernegaSergiy\TableMagic\Table;
use Exception;
use PHPUnit\Framework\TestCase;
use SimpleXMLElement;

class XmlTableExporterTest extends TestCase
{
    private Table $table;

    protected function setUp() : void
    {
        parent::setUp();
        $this->table = new Table(['Name', 'Age']);
        $this->table->addRow(['Alice', 30]);
        $this->table->addRow(['Bob', 25]);
    }

    public function testExportToXml()
    {
        $exporter = new XmlTableExporter();
        $expectedXml = '<?xml version="1.0"?><table><headers><header>Name</header><header>Age</header></headers><rows><row><Name>Alice</Name><Age>30</Age></row><row><Name>Bob</Name><Age>25</Age></row></rows></table>';
        $this->assertXmlStringEqualsXmlString($expectedXml, $exporter->export($this->table));
    }

    public function testExportEmptyTable()
    {
        $emptyTable = new Table();
        $exporter = new XmlTableExporter();
        $expectedXml = '<?xml version="1.0"?><table><headers/><rows/></table>';
        $this->assertXmlStringEqualsXmlString($expectedXml, $exporter->export($emptyTable));
    }

    public function testExportWithEmptyRowsAndValues()
    {
        $table = new Table(['Col1', 'Col2']);
        $table->addRow(['Val1', '']); // Row with empty value
        $table->addRow([]); // Empty row
        $table->addRow(['', 'Val2']); // Row with empty value

        $exporter = new XmlTableExporter();

        $expectedXml = '<?xml version="1.0"?><table><headers><header>Col1</header><header>Col2</header></headers><rows><row><Col1>Val1</Col1><Col2/></row><row><Col1/><Col2/></row><row><Col1/><Col2>Val2</Col2></row></rows></table>';
        $this->assertXmlStringEqualsXmlString($expectedXml, $exporter->export($table));
    }

    public function testToXmlFailsToAddHeadersElement()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Failed to add headers element to XML');

        $table = new Table(['Header']);
        $exporter = new class() extends XmlTableExporter {
            protected function addChildToElement(SimpleXMLElement $element, string $name, ?string $value = null): SimpleXMLElement|false|null
            {
                if ($name === 'headers') {
                    return false;
                }
                return parent::addChildToElement($element, $name, $value);
            }
        };
        $exporter->export($table);
    }

    public function testToXmlFailsToAddHeaderChildElement()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Failed to add header child element to XML');

        $table = new Table(['Header']);
        $exporter = new class() extends XmlTableExporter {
            protected function addChildToElement(SimpleXMLElement $element, string $name, ?string $value = null): SimpleXMLElement|false|null
            {
                if ($name === 'header') {
                    return false;
                }
                return parent::addChildToElement($element, $name, $value);
            }
        };
        $exporter->export($table);
    }

    public function testToXmlFailsToAddRowsElement()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Failed to add rows element to XML');

        $table = new Table(['Header']);
        $exporter = new class() extends XmlTableExporter {
            protected function addChildToElement(SimpleXMLElement $element, string $name, ?string $value = null): SimpleXMLElement|false|null
            {
                if ($name === 'rows') {
                    return false;
                }
                return parent::addChildToElement($element, $name, $value);
            }
        };
        $exporter->export($table);
    }

    public function testToXmlFailsToAddRowElement()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Failed to add row element to XML');

        $table = new Table(['Header']);
        $table->addRow(['Value']);
        $exporter = new class() extends XmlTableExporter {
            protected function addChildToElement(SimpleXMLElement $element, string $name, ?string $value = null): SimpleXMLElement|false|null
            {
                if ($name === 'row') {
                    return false;
                }
                return parent::addChildToElement($element, $name, $value);
            }
        };
        $exporter->export($table);
    }

    public function testToXmlFailsToAddCellChildElement()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Failed to add cell child element to XML');

        $table = new Table(['Header']);
        $table->addRow(['Value']);
        $exporter = new class() extends XmlTableExporter {
            protected function addChildToElement(SimpleXMLElement $element, string $name, ?string $value = null): SimpleXMLElement|false|null
            {
                if ($name !== 'table' && $name !== 'headers' && $name !== 'header' && $name !== 'rows' && $name !== 'row') {
                    return false;
                }
                return parent::addChildToElement($element, $name, $value);
            }
        };
        $exporter->export($table);
    }

    public function testToXmlFailsToGenerateXml()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Failed to generate XML');

        $table = new Table(['Header']);
        $exporter = new class() extends XmlTableExporter {
            protected function convertXmlToString(SimpleXMLElement $element): string|false
            {
                return false;
            }
        };
        $exporter->export($table);
    }
}
