<?php

namespace Tests\Importers;

use ChernegaSergiy\TableMagic\Importers\MarkdownTableImporter;
use PHPUnit\Framework\TestCase;

class MarkdownTableImporterTest extends TestCase
{
    public function testImport()
    {
        $markdown = <<<MARKDOWN
| Name | Age | City |
|:---|:---:|---:|
| Alice | 30 | New York |
| Bob | 25 | London |
MARKDOWN;

        $importer = new MarkdownTableImporter();
        $table = $importer->import($markdown);

        $this->assertEquals(['Name', 'Age', 'City'], $table->headers);
        $this->assertEquals(['l', 'c', 'r'], $table->getAlignments());
        $this->assertEquals([['Alice', '30', 'New York'], ['Bob', '25', 'London']], $table->getRows());
    }
}
