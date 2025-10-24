<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use ChernegaSergiy\TableMagic\Table;
use ChernegaSergiy\TableMagic\TerminalInteraction;

class TerminalInteractionTest extends TestCase
{
    private $input_stream;
    private $output_stream;

    protected function setUp(): void
    {
        $this->input_stream = fopen('php://memory', 'r+');
        $this->output_stream = fopen('php://memory', 'r+');
    }

    protected function tearDown(): void
    {
        fclose($this->input_stream);
        fclose($this->output_stream);
    }

    private function setInput(string $input): void
    {
        fwrite($this->input_stream, $input);
        rewind($this->input_stream);
    }

    private function getOutput(): string
    {
        rewind($this->output_stream);
        return stream_get_contents($this->output_stream);
    }

    public function testConstructor()
    {
        $table = new Table(['Header1', 'Header2']);
        $interaction = new TerminalInteraction($table, 10);

        $this->assertInstanceOf(TerminalInteraction::class, $interaction);
    }

    public function testRunQuitsImmediately()
    {
        $this->setInput("q\n");

        $table = new Table(['Header1', 'Header2']);
        $interaction = new TerminalInteraction($table, 5, $this->input_stream, $this->output_stream);

        $interaction->run();

        $output = $this->getOutput();
        $this->assertStringContainsString('Page 1 of 0', $output);
        $this->assertStringContainsString("Enter 'n' for next page, 'p' for previous page, a page number, 'e' to edit a row, 'a' to add a row, 'd' to delete a row, or 'q' to quit:", $output);
    }

    public function testDisplayCurrentPage()
    {
        $table = new Table(['Name', 'Age']);
        $table->addRow(['Alice', '30']);
        $table->addRow(['Bob', '24']);
        $table->addRow(['Charlie', '35']);

        $interaction = new TerminalInteraction($table, 2, $this->input_stream, $this->output_stream);

        // Access private method using reflection for testing
        $reflection = new \ReflectionClass($interaction);
        $method = $reflection->getMethod('displayCurrentPage');
        $method->setAccessible(true);

        $method->invoke($interaction);

        $output = $this->getOutput();
        $this->assertStringContainsString('Alice', $output);
        $this->assertStringContainsString('Bob', $output);
        $this->assertStringNotContainsString('Charlie', $output);
    }

    public function testRunNextPage()
    {
        $table = new Table(['Name', 'Age']);
        $table->addRow(['Alice', '30']);
        $table->addRow(['Bob', '24']);
        $table->addRow(['Charlie', '35']);

        $this->setInput("n\nq\n"); // Go to next page, then quit

        $interaction = new TerminalInteraction($table, 2, $this->input_stream, $this->output_stream);
        $interaction->run();

        $output = $this->getOutput();
        $this->assertStringContainsString('Page 2 of 2', $output);
        $this->assertStringContainsString('Charlie', $output);
    }

    public function testRunPreviousPage()
    {
        $table = new Table(['Name', 'Age']);
        $table->addRow(['Alice', '30']);
        $table->addRow(['Bob', '24']);
        $table->addRow(['Charlie', '35']);
        $table->addRow(['David', '40']);

        $this->setInput("n\np\nq\n"); // Go to next page, then previous page, then quit

        $interaction = new TerminalInteraction($table, 2, $this->input_stream, $this->output_stream);
        $interaction->run();

        $output = $this->getOutput();
        $this->assertStringContainsString('Page 1 of 2', $output);
        $this->assertStringContainsString('Alice', $output);
    }

    public function testRunSpecificPage()
    {
        $table = new Table(['Name', 'Age']);
        $table->addRow(['Alice', '30']);
        $table->addRow(['Bob', '24']);
        $table->addRow(['Charlie', '35']);
        $table->addRow(['David', '40']);

        $this->setInput("2\nq\n"); // Go to page 2, then quit

        $interaction = new TerminalInteraction($table, 2, $this->input_stream, $this->output_stream);
        $interaction->run();

        $output = $this->getOutput();
        $this->assertStringContainsString('Page 2 of 2', $output);
        $this->assertStringContainsString('Charlie', $output);
    }

    public function testGetTotalPages()
    {
        $table = new Table(['Name', 'Age']);
        $table->addRow(['Alice', '30']);
        $table->addRow(['Bob', '24']);
        $table->addRow(['Charlie', '35']);

        $interaction = new TerminalInteraction($table, 2);

        // Access private method using reflection for testing
        $reflection = new \ReflectionClass($interaction);
        $method = $reflection->getMethod('getTotalPages');
        $method->setAccessible(true);

        $this->assertEquals(2, $method->invoke($interaction));

        $table_single_page = new Table(['Name', 'Age']);
        $table_single_page->addRow(['Alice', '30']);
        $interaction_single_page = new TerminalInteraction($table_single_page, 2);

        $this->assertEquals(1, $method->invoke($interaction_single_page));
    }

    public function testRunEditRow()
    {
        $table = new Table(['Name', 'Age']);
        $table->addRow(['Alice', '30']);
        $table->addRow(['Bob', '24']);

        $this->setInput("e\n1\nBob Smith\n31\nq\n"); // Edit row 1, change Name to Bob Smith, Age to 31, then quit

        $interaction = new TerminalInteraction($table, 5, $this->input_stream, $this->output_stream);
        $interaction->run();

        $output = $this->getOutput();
        $this->assertStringContainsString('Row updated successfully.', $output);
        $this->assertStringContainsString('Bob Smith', $output);
        $this->assertStringContainsString('31', $output);
    }

    public function testRunAddRow()
    {
        $table = new Table(['Name', 'Age']);
        $table->addRow(['Alice', '30']);

        $this->setInput("a\nCharlie\n35\nq\n"); // Add a new row, then quit

        $interaction = new TerminalInteraction($table, 5, $this->input_stream, $this->output_stream);
        $interaction->run();

        $output = $this->getOutput();
        $this->assertStringContainsString('Row added successfully.', $output);
        $this->assertStringContainsString('Charlie', $output);
        $this->assertStringContainsString('35', $output);
    }

    public function testRunDeleteRow()
    {
        $table = new Table(['Name', 'Age']);
        $table->addRow(['Alice', '30']);
        $table->addRow(['Bob', '24']);

        $this->setInput("d\n1\nq\n"); // Delete row 1, then quit

        $interaction = new TerminalInteraction($table, 5, $this->input_stream, $this->output_stream);
        $interaction->run();

        $output = $this->getOutput();
        $this->assertStringContainsString('Row deleted successfully.', $output);
        $this->assertStringContainsString('Bob', $output);
    }

    public function testEditRowInvalidRowNumber()
    {
        $table = new Table(['Name', 'Age']);
        $table->addRow(['Alice', '30']);

        $this->setInput("e\n99\nq\n"); // Try to edit invalid row, then quit

        $interaction = new TerminalInteraction($table, 5, $this->input_stream, $this->output_stream);
        $interaction->run();

        $output = $this->getOutput();
        $this->assertStringContainsString('Invalid row number.', $output);
    }

    public function testDeleteRowInvalidRowNumber()
    {
        $table = new Table(['Name', 'Age']);
        $table->addRow(['Alice', '30']);

        $this->setInput("d\n99\nq\n"); // Try to delete invalid row, then quit

        $interaction = new TerminalInteraction($table, 5, $this->input_stream, $this->output_stream);
        $interaction->run();

        $output = $this->getOutput();
        $this->assertStringContainsString('Invalid row number.', $output);
    }

    public function testRunFgetsReturnsFalse()
    {
        $table = new Table(['Header1', 'Header2']);
        $interaction = new TerminalInteraction($table, 5, $this->input_stream, $this->output_stream);

        $this->setInput(""); // Provide empty input to simulate EOF

        $interaction->run();

        $output = $this->getOutput();
        $this->assertStringContainsString('Page 1 of 0', $output);
        // Expect the loop to break due to no input
    }

    public function testEditRowFgetsReturnsFalse()
    {
        $table = new Table(['Name', 'Age']);
        $table->addRow(['Alice', '30']);

        $this->setInput("e\n"); // Enter 'e', then simulate false for row number input

        $interaction = new TerminalInteraction($table, 5, $this->input_stream, $this->output_stream);

        $interaction->run();

        $output = $this->getOutput();
        $this->assertStringContainsString('Enter the row number to edit', $output);
        // Expect no row update message
        $this->assertStringNotContainsString('Row updated successfully.', $output);
    }

    public function testAddRowFgetsReturnsFalse()
    {
        $table = new Table(['Name', 'Age']);

        $this->setInput("a\n"); // Enter 'a', then simulate false for value input

        $interaction = new TerminalInteraction($table, 5, $this->input_stream, $this->output_stream);

        $interaction->run();

        $output = $this->getOutput();
        $this->assertStringContainsString('Enter value for', $output);
        // Expect no row added message
        $this->assertStringNotContainsString('Row added successfully.', $output);
    }

    public function testDeleteRowFgetsReturnsFalse()
    {
        $table = new Table(['Name', 'Age']);
        $table->addRow(['Alice', '30']);

        $this->setInput("d\n"); // Enter 'd', then simulate false for row number input

        $interaction = new TerminalInteraction($table, 5, $this->input_stream, $this->output_stream);

        $interaction->run();

        $output = $this->getOutput();
        $this->assertStringContainsString('Enter the row number to delete', $output);
        // Expect no row deleted message
        $this->assertStringNotContainsString('Row deleted successfully.', $output);
    }

    public function testEditRowFgetsReturnsFalseForNewValue()
    {
        $table = new Table(['Name', 'Age']);
        $table->addRow(['Alice', '30']);

        $this->setInput("e\n1\n"); // Enter 'e', then row number 1, then simulate false for new value input

        $interaction = new TerminalInteraction($table, 5, $this->input_stream, $this->output_stream);

        $interaction->run();

        $output = $this->getOutput();
        $this->assertStringContainsString("Enter new value for 'Name'", $output);
        $this->assertStringContainsString('No changes made to row.', $output);
        $this->assertStringContainsString('Alice', $output);
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


    public function testEditRowNoChangesMade()
    {
        $table = new Table(['Name', 'Age']);
        $table->addRow(['Alice', '30']);

        $this->setInput("e\n1\n\n\nq\n"); // Edit row 1, provide empty input for Name and Age, then quit

        $interaction = new TerminalInteraction($table, 5, $this->input_stream, $this->output_stream);
        $interaction->run();

        $output = $this->getOutput();
        $this->assertStringContainsString('No changes made to row.', $output);
        $this->assertStringContainsString('Alice', $output); // Ensure original data is still there
        $this->assertStringContainsString('30', $output);
    }

    public function testEditRowFgetsReturnsFalseDirectly()
    {
        $table = new Table(['Name', 'Age']);
        $table->addRow(['Alice', '30']);

        // Simulate input for row number, then empty for new values
        $this->setInput("1\n");

        $interaction = new TerminalInteraction($table, 5, $this->input_stream, $this->output_stream);

        // Call editRow directly using reflection
        $reflection = new \ReflectionClass($interaction);
        $method = $reflection->getMethod('editRow');
        $method->setAccessible(true);

        $method->invoke($interaction);

        $output = $this->getOutput();
        $this->assertStringContainsString('Enter the row number to edit', $output);
        $this->assertStringContainsString("Enter new value for 'Name'", $output);
        $this->assertStringContainsString('No changes made to row.', $output);
        $this->assertStringContainsString('Alice', $output);
    }

}
