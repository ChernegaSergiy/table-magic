<?php

use ChernegaSergiy\TableMagic\Table;
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
}