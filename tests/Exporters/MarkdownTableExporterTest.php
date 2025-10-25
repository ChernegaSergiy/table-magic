<?php

namespace Tests\Exporters;

use ChernegaSergiy\TableMagic\Table;
use ChernegaSergiy\TableMagic\Exporters\MarkdownTableExporter;
use PHPUnit\Framework\TestCase;

class MarkdownTableExporterTest extends TestCase
{
    public function testExport()
    {
        $headers = ['Name', 'Age', 'City'];
        $alignments = ['Name' => 'l', 'Age' => 'c', 'City' => 'r'];
        $table = new Table($headers, $alignments);
        $table->addRow(['Alice', 30, 'New York']);
        $table->addRow(['Bob', 25, 'London']);

        $exporter = new MarkdownTableExporter();
        $markdown = $exporter->export($table);

        $expected = <<<MARKDOWN
| Name | Age | City |
|:---|:---:|---:|
| Alice | 30 | New York |
| Bob | 25 | London |
MARKDOWN;

        $this->assertEquals($expected, $markdown);
    }
}
