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

    public function testConstructorAssignsTable()
    {
        $table = new Table(['Header1']);
        $exporter = new TableExporter($table);

        $reflection = new \ReflectionClass($exporter);
        $tableProperty = $reflection->getProperty('table');
        $tableProperty->setAccessible(true);

        $this->assertSame($table, $tableProperty->getValue($exporter));
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

    public function testExportWithEmptyRowsAndValues()
    {
        $table = new Table(['Col1', 'Col2']);
        $table->addRow(['Val1', '']); // Row with empty value
        $table->addRow([]); // Empty row
        $table->addRow(['', 'Val2']); // Row with empty value

        $exporter = new TableExporter($table);

        // CSV
        $expectedCsv = "Col1,Col2\nVal1,\n,\n,Val2\n";
        $this->assertEquals($expectedCsv, $exporter->export('csv'));

        // JSON
        $expectedJson = '{"headers":["Col1","Col2"],"rows":[["Val1",""],["",""],["","Val2"]]}';
        $this->assertEquals($expectedJson, $exporter->export('json'));

        // XML
        $expectedXml = '<?xml version="1.0"?><table><headers><header>Col1</header><header>Col2</header></headers><rows><row><Col1>Val1</Col1><Col2/></row><row><Col1/><Col2/></row><row><Col1/><Col2>Val2</Col2></row></rows></table>';
        $this->assertXmlStringEqualsXmlString($expectedXml, $exporter->export('xml'));
    }

    public function testExportToJsonWithInvalidUtf8Data(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Failed to encode data to JSON');

        $table = new Table(['Col1']);
        // Add a row with an explicitly invalid UTF-8 sequence
        $table->addRow(["invalid\x80utf8"]);

        $exporter = new TableExporter($table);
        $exporter->export('json');
    }

    public function testToCsvFailsToOpenStream()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Failed to open temporary stream');

        $table = new Table(['Header']);
        $exporter = new class($table) extends TableExporter {
            protected function openTemporaryStream()
            {
                return false;
            }
        };
        $exporter->export('csv');
    }

    public function testToXmlFailsToAddHeadersElement()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Failed to add headers element to XML');

        $table = new Table(['Header']);
        $exporter = new class($table) extends TableExporter {
            protected function addChildToElement(SimpleXMLElement $element, string $name, ?string $value = null): SimpleXMLElement|false|null
            {
                if ($name === 'headers') {
                    return false;
                }
                return parent::addChildToElement($element, $name, $value);
            }
        };
        $exporter->export('xml');
    }

    public function testToXmlFailsToAddHeaderChildElement()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Failed to add header child element to XML');

        $table = new Table(['Header']);
        $exporter = new class($table) extends TableExporter {
            protected function addChildToElement(SimpleXMLElement $element, string $name, ?string $value = null): SimpleXMLElement|false|null
            {
                if ($name === 'header') {
                    return false;
                }
                return parent::addChildToElement($element, $name, $value);
            }
        };
        $exporter->export('xml');
    }

    public function testToXmlFailsToAddRowsElement()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Failed to add rows element to XML');

        $table = new Table(['Header']);
        $exporter = new class($table) extends TableExporter {
            protected function addChildToElement(SimpleXMLElement $element, string $name, ?string $value = null): SimpleXMLElement|false|null
            {
                if ($name === 'rows') {
                    return false;
                }
                return parent::addChildToElement($element, $name, $value);
            }
        };
        $exporter->export('xml');
    }

    public function testToXmlFailsToAddRowElement()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Failed to add row element to XML');

        $table = new Table(['Header']);
        $table->addRow(['Value']);
        $exporter = new class($table) extends TableExporter {
            protected function addChildToElement(SimpleXMLElement $element, string $name, ?string $value = null): SimpleXMLElement|false|null
            {
                if ($name === 'row') {
                    return false;
                }
                return parent::addChildToElement($element, $name, $value);
            }
        };
        $exporter->export('xml');
    }

    public function testToXmlFailsToAddCellChildElement()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Failed to add cell child element to XML');

        $table = new Table(['Header']);
        $table->addRow(['Value']);
        $exporter = new class($table) extends TableExporter {
            protected function addChildToElement(SimpleXMLElement $element, string $name, ?string $value = null): SimpleXMLElement|false|null
            {
                if ($name !== 'table' && $name !== 'headers' && $name !== 'header' && $name !== 'rows' && $name !== 'row') {
                    return false;
                }
                return parent::addChildToElement($element, $name, $value);
            }
        };
        $exporter->export('xml');
    }

    public function testToXmlFailsToGenerateXml()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Failed to generate XML');

        $table = new Table(['Header']);
        $exporter = new class($table) extends TableExporter {
            protected function convertXmlToString(SimpleXMLElement $element): string|false
            {
                return false;
            }
        };
        $exporter->export('xml');
    }
}
