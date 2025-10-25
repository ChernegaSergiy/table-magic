<?php

namespace Tests\Exporters;

use ChernegaSergiy\TableMagic\Exporters\HtmlTableExporter;
use ChernegaSergiy\TableMagic\Table;
use PHPUnit\Framework\TestCase;

class HtmlTableExporterTest extends TestCase
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
        $exporter = new HtmlTableExporter();
        $expectedHtml = '<table border="1"><thead><tr><th>Name</th><th>Age</th></tr></thead><tbody><tr><td>Alice</td><td>30</td></tr><tr><td>Bob</td><td>25</td></tr></tbody></table>';
        $this->assertEquals($expectedHtml, $exporter->export($this->table));
    }

    public function testExportEmptyTable()
    {
        $emptyTable = new Table();
        $exporter = new HtmlTableExporter();
        $expectedHtml = '<table border="1"><thead><tr></tr></thead><tbody></tbody></table>';
        $this->assertEquals($expectedHtml, $exporter->export($emptyTable));
    }
}
