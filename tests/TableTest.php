<?php

use ChernegaSergiy\TableMagic\Table;
use ChernegaSergiy\TableMagic\TableStyle;
use ChernegaSergiy\TableMagic\TableStyleRegistry;
use PHPUnit\Framework\TestCase;

class TableTest extends TestCase
{
    public function testAddRow()
    {
        $table = new Table(['Name', 'Age']);
        $table->addRow(['Alice', 30]);
        $this->assertEquals('Alice', $table->getRows()[0][0]);
        $this->assertEquals(30, $table->getRows()[0][1]);
    }

    public function testAddColumn()
    {
        $table = new Table(['Name']);
        $table->addRow(['Alice']);
        $table->addColumn('Age', [30]);
        $this->assertEquals('Age', $table->headers[1]);
        $this->assertEquals(30, $table->getRows()[0][1]);
    }

    public function testSortTable()
    {
        $table = new Table(['Name', 'Age']);
        $table->addRow(['Alice', 30]);
        $table->addRow(['Bob', 25]);
        $table->sortTable('Age');
        $this->assertEquals('Bob', $table->getRows()[0][0]);
        $this->assertEquals(25, $table->getRows()[0][1]);
    }

    public function testRemoveDivider()
    {
        $table = new Table(['Name']);
        $table->addRow(['Alice'], true);
        $table->removeDivider(0);
        $this->assertFalse($table->hasDivider(0));
    }

    public function testGetTable()
    {
        $table = new Table(['Name', 'Age']);
        $table->addRow(['Alice', 30]);
        $expected = "+-------+-----+\n| Name  | Age |\n+-------+-----+\n| Alice | 30  |\n+-------+-----+\n";
        $this->assertEquals($expected, $table->getTable());
    }

    public function testAddRows()
    {
        $table = new Table(['Name', 'Age']);
        $rows = [
            ['Alice', 30],
            ['Bob', 25],
        ];
        $table->addRows($rows);
        $this->assertEquals('Alice', $table->getRows()[0][0]);
        $this->assertEquals(30, $table->getRows()[0][1]);
        $this->assertEquals('Bob', $table->getRows()[1][0]);
        $this->assertEquals(25, $table->getRows()[1][1]);
    }

    public function testAddRowsWithDividers()
    {
        $table = new Table(['Name', 'Age']);
        $rows = [
            ['Alice', 30],
            ['Bob', 25],
        ];
        $dividers = [1 => true];
        $table->addRows($rows, $dividers);
        $this->assertFalse($table->hasDivider(0));
        $this->assertTrue($table->hasDivider(1));

        $expected = "+-------+-----+\n| Name  | Age |\n+-------+-----+\n| Alice | 30  |\n| Bob   | 25  |\n+-------+-----+\n+-------+-----+\n";
        $this->assertEquals($expected, $table->getTable());
    }

    public function testRemoveInvalidDivider()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Row index 1 is invalid.');
        $table = new Table(['Name']);
        $table->addRow(['Alice']);
        $table->removeDivider(1);
    }

    public function testGetEmptyTable()
    {
        $table = new Table();
        $this->assertEquals('Empty table', $table->getTable());
    }

    public function testSortTableWithInvalidColumn()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Column 'Invalid' not found.");
        $table = new Table(['Name', 'Age']);
        $table->addRow(['Alice', 30]);
        $table->sortTable('Invalid');
    }

    public function testSortTableWithString()
    {
        $table = new Table(['Name', 'City']);
        $table->addRow(['Bob', 'New York']);
        $table->addRow(['Alice', 'Los Angeles']);
        $table->sortTable('Name');
        $this->assertEquals('Alice', $table->getRows()[0][0]);
        $this->assertEquals('Los Angeles', $table->getRows()[0][1]);
        $this->assertEquals('Bob', $table->getRows()[1][0]);
        $this->assertEquals('New York', $table->getRows()[1][1]);
    }

    public function testToString()
    {
        $table = new Table(['Name', 'Age']);
        $table->addRow(['Alice', 30]);
        $expected = "+-------+-----+\n| Name  | Age |\n+-------+-----+\n| Alice | 30  |\n+-------+-----+\n";
        $this->assertEquals($expected, (string)$table);
    }

    public function testGetColWidths()
    {
        $table = new Table(['Header1', 'Header2']);
        $table->addRow(['Value1', 'LongerValue2']);
        $table->addRow(['EvenLongerValue1', 'Value2']);
        $expected_widths = [16, 12]; // 'EvenLongerValue1' (16) and 'LongerValue2' (12)
        $this->assertEquals($expected_widths, $table->getColWidths());
    }

    public function testGetRowInvalidIndexThrowsException()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Row index 0 is invalid.');
        $table = new Table(['Name']);
        $table->getRow(0);
    }

    public function testUpdateRowInvalidIndexThrowsException()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Row index 0 is invalid.');
        $table = new Table(['Name']);
        $table->updateRow(0, ['NewName']);
    }

    public function testDeleteRowInvalidIndexThrowsException()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Row index 0 is invalid.');
        $table = new Table(['Name']);
        $table->deleteRow(0);
    }

    public function testSetStyle()
    {
        $table = new Table(['Name', 'Age']);
        $table->addRow(['Alice', 30]);

        // Test default style
        $table->setStyle('default');
        $default_output = $table->getTable();
        $this->assertStringContainsString('+-------', $default_output);
        $this->assertStringContainsString('| Name  |', $default_output);

        // Test GitHub Markdown style
        $table->setStyle('github-markdown');
        $markdown_output = $table->getTable();
        $this->assertStringStartsNotWith('+', $markdown_output);
        $this->assertStringContainsString('| Name  |', $markdown_output);
        $this->assertStringContainsString('|-------|', $markdown_output);
        $this->assertStringNotContainsString('+-------', $markdown_output);

        // Test Unicode Single Line style
        $table->setStyle('unicode-single-line');
        $unicode_output = $table->getTable();
        $this->assertStringContainsString('┌───────', $unicode_output);
        $this->assertStringContainsString('│ Name  │', $unicode_output);
        $this->assertStringContainsString('├───────', $unicode_output);
        $this->assertStringContainsString('└───────', $unicode_output);

        // Test reStructuredTextGrid style
        $table->setStyle('restructured-text-grid');
        $rst_output = $table->getTable();
        $this->assertStringContainsString('+=======+', $rst_output);

        // Test unicodeDoubleLine style
        $table->setStyle('unicode-double-line');
        $double_output = $table->getTable();
        $this->assertStringContainsString('╔═══════', $double_output);

        // Test dots style
        $table->setStyle('dots');
        $dots_output = $table->getTable();
        $this->assertStringContainsString('.........', $dots_output);
        $this->assertStringContainsString(': Name  :', $dots_output);

        // Test compact style
        $table->setStyle('compact');
        $compact_output = $table->getTable();
        $this->assertStringStartsWith('  ', $compact_output); // Starts with two spaces due to vertical separator being a space
        $this->assertStringContainsString('  Name    Age  ', $compact_output); // Header line
        $this->assertStringContainsString(' ------- ----- ', $compact_output); // Separator line
        $this->assertStringNotContainsString('|', $compact_output); // No vertical lines
        $this->assertStringNotContainsString('+', $compact_output); // No cross characters

        // Test reStructuredText Simple style
        $table->setStyle('restructured-text-simple');
        $rst_simple_output = $table->getTable();
        $this->assertStringContainsString(' ======= ===== ', $rst_simple_output);
        $this->assertStringContainsString('  Name    Age  ', $rst_simple_output);
        $this->assertStringNotContainsString('|', $rst_simple_output);
        $this->assertStringNotContainsString('+', $rst_simple_output);

        // Test reddit-markdown style
        $table->setStyle('reddit-markdown');
        $reddit_output = $table->getTable();
        $this->assertStringStartsWith('|', $reddit_output);
        $this->assertStringContainsString(' Name  | Age ', $reddit_output); // Header line
        $this->assertStringContainsString(' -------|----- ', $reddit_output); // Separator line

        // Test rounded style
        $table->setStyle('rounded');
        $rounded_output = $table->getTable();
        $this->assertStringContainsString('.-------.', $rounded_output);
        $this->assertStringContainsString('| Name  | Age |', $rounded_output);
        $this->assertStringContainsString(':-------+-----:', $rounded_output);
        $this->assertStringContainsString('\'-------\'-----\'', $rounded_output);
    }

    public function testCustomStyleRegistration()
    {
        $ugly_chaos_style = new TableStyle(
            'O',
            ['!', 'X', '$', '@'],
            ['O', 'X', '#', 'O'],
            ['O', 'X', '$', 'O'],
            ['%', 'X', '$', '&']
        );
        TableStyleRegistry::register('ugly-chaos', $ugly_chaos_style);

        $table = new Table(['Name', 'Age']);
        $table->addRow(['Alice', 30]);
        $table->setStyle('ugly-chaos');

        $output = $table->getTable();

        $this->assertMatchesRegularExpression('/!XXXXXXX\$XXXXX@\r?\n/', $output);
        $this->assertStringContainsString("O Name  O Age O", $output);
        $this->assertStringContainsString("OXXXXXXX#XXXXXO", $output);
        $this->assertStringContainsString("O Alice O 30  O", $output);
        $this->assertMatchesRegularExpression('/%XXXXXXX\$XXXXX&\r?\n?$/', $output);
    }

    public function testSetAlignmentNonExistentColumn()
    {
        $table = new Table(['Header1', 'Header2']);
        $table->setAlignment('NonExistent', 'c');

        // Access private property using reflection for testing
        $reflection = new \ReflectionClass($table);
        $alignments_property = $reflection->getProperty('alignments');
        $alignments_property->setAccessible(true);
        $alignments = $alignments_property->getValue($table);

        // Expect the last column's alignment to be set to 'c'
        $this->assertEquals('c', $alignments[count($table->headers) - 1]);
    }

    public function testGetInvalidStyleThrowsException()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Style "non-existent-style" is not registered.');

        $table = new Table();
        $table->setStyle('non-existent-style');
    }
}
