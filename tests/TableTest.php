<?php

use PHPUnit\Framework\TestCase;
use ChernegaSergiy\TableMagic\Table;

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
        $this->assertFalse($table->dividers[0]);
    }

    public function testGetTable()
    {
        $table = new Table(['Name', 'Age']);
        $table->addRow(['Alice', 30]);
        $expected = "+-------+-----+\n| Name  | Age |\n+-------+-----+\n| Alice | 30  |\n+-------+-----+\n";
        $this->assertEquals($expected, $table->getTable());
    }
}
