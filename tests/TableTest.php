<?php

use ChernegaSergiy\TableMagic\Table;
use PHPUnit\Framework\TestCase;

class TableTest extends TestCase
{
    public function testAddRow()
    {
        $table = new Table(['Name', 'Age']);
        $table->addRow(['Alice', 30]);
        $this->assertEquals('Alice', $table->rows[0][0]);
        $this->assertEquals(30, $table->rows[0][1]);
    }

    public function testAddColumn()
    {
        $table = new Table(['Name']);
        $table->addRow(['Alice']);
        $table->addColumn('Age', [30]);
        $this->assertEquals('Age', $table->headers[1]);
        $this->assertEquals(30, $table->rows[0][1]);
    }

    public function testSortTable()
    {
        $table = new Table(['Name', 'Age']);
        $table->addRow(['Alice', 30]);
        $table->addRow(['Bob', 25]);
        $table->sortTable('Age');
        $this->assertEquals('Bob', $table->rows[0][0]);
        $this->assertEquals(25, $table->rows[0][1]);
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
        $this->assertEquals('Alice', $table->rows[0][0]);
        $this->assertEquals(30, $table->rows[0][1]);
        $this->assertEquals('Bob', $table->rows[1][0]);
        $this->assertEquals(25, $table->rows[1][1]);
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
        $this->assertEquals('Alice', $table->rows[0][0]);
        $this->assertEquals('Los Angeles', $table->rows[0][1]);
        $this->assertEquals('Bob', $table->rows[1][0]);
        $this->assertEquals('New York', $table->rows[1][1]);
    }

    public function testToString()
    {
        $table = new Table(['Name', 'Age']);
        $table->addRow(['Alice', 30]);
        $expected = "+-------+-----+\n| Name  | Age |\n+-------+-----+\n| Alice | 30  |\n+-------+-----+\n";
        $this->assertEquals($expected, (string)$table);
    }
}
