<?php

namespace Tests;

use ChernegaSergiy\TableMagic\TableExporter;
use PHPUnit\Framework\TestCase;
use ChernegaSergiy\TableMagic\Table;
use ChernegaSergiy\TableMagic\TerminalInteraction;

class TerminalInteractionTest extends TestCase
{
    private $input_stream;
    private $output_stream;

    private string $test_file = 'test_output.csv';

    protected function setUp(): void
    {
        $this->input_stream = fopen('php://memory', 'r+');
        $this->output_stream = fopen('php://memory', 'r+');
        if (file_exists($this->test_file)) {
            unlink($this->test_file);
        }
    }

    protected function tearDown(): void
    {
        fclose($this->input_stream);
        fclose($this->output_stream);
        if (file_exists($this->test_file)) {
            unlink($this->test_file);
        }
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
        $interaction = new TerminalInteraction($table, 10, $this->input_stream, $this->output_stream, null);

        $this->assertInstanceOf(TerminalInteraction::class, $interaction);
    }

    public function testRunQuitsImmediately()
    {
        $this->setInput("q\n");

        $table = new Table(['Header1', 'Header2']);
        $interaction = new TerminalInteraction($table, 5, $this->input_stream, $this->output_stream, null);

        $interaction->run();

        $output = $this->getOutput();
        $this->assertStringContainsString('Page 1 of 0', $output);
        $this->assertStringContainsString("Enter 'n' for next page, 'p' for previous page, a page number, 'e' to edit a row, 'a' to add a row, 'd' to delete a row, 's' to sort, 'x' to export, or 'q' to quit:", $output);
    }

    public function testDisplayCurrentPage()
    {
        $table = new Table(['Name', 'Age']);
        $table->addRow(['Alice', '30']);
        $table->addRow(['Bob', '24']);
        $table->addRow(['Charlie', '35']);

        $interaction = new TerminalInteraction($table, 2, $this->input_stream, $this->output_stream, null);

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

        $interaction = new TerminalInteraction($table, 2, $this->input_stream, $this->output_stream, null);
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

        $interaction = new TerminalInteraction($table, 2, $this->input_stream, $this->output_stream, null);
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

        $interaction = new TerminalInteraction($table, 2, $this->input_stream, $this->output_stream, null);
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

        $interaction = new TerminalInteraction($table, 2, $this->input_stream, $this->output_stream, null);

        // Access private method using reflection for testing
        $reflection = new \ReflectionClass($interaction);
        $method = $reflection->getMethod('getTotalPages');
        $method->setAccessible(true);

        $this->assertEquals(2, $method->invoke($interaction));

        $table_single_page = new Table(['Name', 'Age']);
        $table_single_page->addRow(['Alice', '30']);
        $interaction_single_page = new TerminalInteraction($table_single_page, 2, $this->input_stream, $this->output_stream, null);

        $this->assertEquals(1, $method->invoke($interaction_single_page));
    }

    public function testRunEditRow()
    {
        $table = new Table(['Name', 'Age']);
        $table->addRow(['Alice', '30']);
        $table->addRow(['Bob', '24']);

        $this->setInput("e\n1\nBob Smith\n31\nq\n"); // Edit row 1, change Name to Bob Smith, Age to 31, then quit

        $interaction = new TerminalInteraction($table, 5, $this->input_stream, $this->output_stream, null);
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

        $interaction = new TerminalInteraction($table, 5, $this->input_stream, $this->output_stream, null);
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

        $interaction = new TerminalInteraction($table, 5, $this->input_stream, $this->output_stream, null);
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

        $interaction = new TerminalInteraction($table, 5, $this->input_stream, $this->output_stream, null);
        $interaction->run();

        $output = $this->getOutput();
        $this->assertStringContainsString('Invalid row number.', $output);
    }

    public function testDeleteRowInvalidRowNumber()
    {
        $table = new Table(['Name', 'Age']);
        $table->addRow(['Alice', '30']);

        $this->setInput("d\n99\nq\n"); // Try to delete invalid row, then quit

        $interaction = new TerminalInteraction($table, 5, $this->input_stream, $this->output_stream, null);
        $interaction->run();

        $output = $this->getOutput();
        $this->assertStringContainsString('Invalid row number.', $output);
    }

    public function testRunFgetsReturnsFalse()
    {
        $table = new Table(['Header1', 'Header2']);
        $interaction = new TerminalInteraction($table, 5, $this->input_stream, $this->output_stream, null);

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

        $interaction = new TerminalInteraction($table, 5, $this->input_stream, $this->output_stream, null);

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

        $interaction = new TerminalInteraction($table, 5, $this->input_stream, $this->output_stream, null);

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

        $interaction = new TerminalInteraction($table, 5, $this->input_stream, $this->output_stream, null);

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

        $interaction = new TerminalInteraction($table, 5, $this->input_stream, $this->output_stream, null);

        $interaction->run();

        $output = $this->getOutput();
        $this->assertStringContainsString("Enter new value for 'Name'", $output);
        $this->assertStringContainsString('No changes made to row.', $output);
        $this->assertStringContainsString('Alice', $output);
    }




    public function testEditRowNoChangesMade()
    {
        $table = new Table(['Name', 'Age']);
        $table->addRow(['Alice', '30']);

        $this->setInput("e\n1\n\n\nq\n"); // Edit row 1, provide empty input for Name and Age, then quit

        $interaction = new TerminalInteraction($table, 5, $this->input_stream, $this->output_stream, null);
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

        $interaction = new TerminalInteraction($table, 5, $this->input_stream, $this->output_stream, null);

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

    public function testRunExportTableSuccessfully()
    {
        $table = new Table(['Name', 'Age']);
        $table->addRow(['Alice', '30']);

        $this->setInput("x\ncsv\n" . $this->test_file . "\nq\n");

        $interaction = new TerminalInteraction($table, 5, $this->input_stream, $this->output_stream, null);
        $interaction->run();

        $output = $this->getOutput();
        $this->assertStringContainsString('Table exported successfully to ', $output);
        $this->assertFileExists($this->test_file);
        $this->assertEquals("Name,Age\nAlice,30\n", file_get_contents($this->test_file));
    }

    public function testRunExportTableInvalidFormatThenValid()
    {
        $table = new Table(['Name', 'Age']);
        $table->addRow(['Alice', '30']);

        $this->setInput("x\ninvalid\ncsv\n" . $this->test_file . "\nq\n");

        $interaction = new TerminalInteraction($table, 5, $this->input_stream, $this->output_stream, null);
        $interaction->run();

        $output = $this->getOutput();
        $this->assertStringContainsString('Invalid format.', $output);
        $this->assertStringContainsString('Table exported successfully to ', $output);
        $this->assertFileExists($this->test_file);
        $this->assertEquals("Name,Age\nAlice,30\n", file_get_contents($this->test_file));
    }

    public function testRunExportTableCancelOnFormatPrompt()
    {
        $table = new Table(['Name', 'Age']);
        $table->addRow(['Alice', '30']);

        $this->setInput("x\n\nq\n"); // Enter x, then empty input for format (simulates false from fgets), then quit

        $interaction = new TerminalInteraction($table, 5, $this->input_stream, $this->output_stream, null);
        $interaction->run();

        $output = $this->getOutput();
        $this->assertStringContainsString('Export cancelled.', $output);
        $this->assertFileDoesNotExist($this->test_file);
    }

    public function testRunExportTableCancelOnFilenamePrompt()
    {
        $table = new Table(['Name', 'Age']);
        $table->addRow(['Alice', '30']);

        // Simulate 'x', then 'csv', then EOF for filename input
        $this->setInput("x\ncsv\n"); // No trailing newline after csv, so next fgets will return false

        $interaction = new TerminalInteraction($table, 5, $this->input_stream, $this->output_stream, null);
        $interaction->run();

        $output = $this->getOutput();
        $this->assertStringContainsString('Export cancelled.', $output);
        $this->assertFileDoesNotExist($this->test_file);
    }

    public function testRunExportTableOverwriteExistingFile()
    {
        $table = new Table(['Name', 'Age']);
        $table->addRow(['Alice', '30']);

        file_put_contents($this->test_file, "Old content\n");

        $this->setInput("x\ncsv\n" . $this->test_file . "\ny\nq\n"); // Export, provide existing filename, confirm overwrite, then quit

        $interaction = new TerminalInteraction($table, 5, $this->input_stream, $this->output_stream, null);
        $interaction->run();

        $output = $this->getOutput();
        $this->assertStringContainsString("File '{$this->test_file}' already exists. Overwrite? (y/n): ", $output);
        $this->assertStringContainsString('Table exported successfully to ', $output);
        $this->assertEquals("Name,Age\nAlice,30\n", file_get_contents($this->test_file));
    }

    public function testRunExportTableDoNotOverwriteExistingFile()
    {
        $table = new Table(['Name', 'Age']);
        $table->addRow(['Alice', '30']);

        file_put_contents($this->test_file, "Old content\n");

        $this->setInput("x\ncsv\n" . $this->test_file . "\nn\nq\n"); // Export, provide existing filename, deny overwrite, then quit

        $interaction = new TerminalInteraction($table, 5, $this->input_stream, $this->output_stream, null);
        $interaction->run();

        $output = $this->getOutput();
        $this->assertStringContainsString("File '{$this->test_file}' already exists. Overwrite? (y/n): ", $output);
        $this->assertStringContainsString('Export cancelled.', $output);
        $this->assertEquals("Old content\n", file_get_contents($this->test_file)); // Content should remain unchanged
    }

    public function testRunExportTableExportFails()
    {
        $table = new Table(['Name', 'Age']);
        $table->addRow(['Alice', '30']);

        // Mock TableExporter to throw an exception during export
        $mockTableExporter = $this->createMock(TableExporter::class);
        $mockTableExporter->method('export')
                          ->willThrowException(new \Exception('Simulated export failure'));

        // Create a TerminalInteraction instance with the mock exporter
        $this->setInput("x\ncsv\n" . $this->test_file . "\nq\n");
        $interaction = new TerminalInteraction($table, 5, $this->input_stream, $this->output_stream, $mockTableExporter);
        $interaction->run();

        $output = $this->getOutput();
        $this->assertStringContainsString('Error exporting table: Simulated export failure', $output);
        $this->assertFileDoesNotExist($this->test_file);
    }

    public function testRunExportTableEmptyFilenameThenValid()
    {
        $table = new Table(['Name', 'Age']);
        $table->addRow(['Alice', '30']);

        $this->setInput("x\ncsv\n\n" . $this->test_file . "\nq\n"); // Export, csv, empty filename, then valid filename, then quit

        $interaction = new TerminalInteraction($table, 5, $this->input_stream, $this->output_stream, null);
        $interaction->run();

        $output = $this->getOutput();
        $this->assertStringContainsString('Filename cannot be empty.', $output);
        $this->assertStringContainsString('Table exported successfully to ', $output);
        $this->assertFileExists($this->test_file);
        $this->assertEquals("Name,Age\nAlice,30\n", file_get_contents($this->test_file));
    }

    public function testRunSortTable()
    {
        $table = new Table(['Name', 'Age']);
        $table->addRow(['Charlie', '35']);
        $table->addRow(['Alice', '30']);
        $table->addRow(['Bob', '24']);

        $this->setInput("s\nName\nasc\nq\n"); // Sort by Name ascending, then quit

        $interaction = new TerminalInteraction($table, 5, $this->input_stream, $this->output_stream, null);
        $interaction->run();

        $output = $this->getOutput();
        $this->assertStringContainsString('Table sorted by \'Name\' in ascending order.', $output);
        $this->assertStringContainsString("Alice", $output);
        $this->assertStringContainsString("Bob", $output);
        $this->assertStringContainsString("Charlie", $output);

        // Extract the last rendered table for order verification
        $last_table_header_start = strrpos($output, '|  Name   | Age |');
        $relevant_output = substr($output, $last_table_header_start);

        // Verify order (this is a bit tricky with string contains, but we can check relative positions)
        $alice_pos = strpos($relevant_output, 'Alice');
        $bob_pos = strpos($relevant_output, 'Bob');
        $charlie_pos = strpos($relevant_output, 'Charlie');

        $this->assertTrue($alice_pos < $bob_pos);
        $this->assertTrue($bob_pos < $charlie_pos);

        // Reset input and output streams for the next interaction
        rewind($this->input_stream);
        ftruncate($this->input_stream, 0);
        rewind($this->output_stream);
        ftruncate($this->output_stream, 0);

        $table_desc = new Table(['Name', 'Age']);
        $table_desc->addRow(['Charlie', '35']);
        $table_desc->addRow(['Alice', '30']);
        $table_desc->addRow(['Bob', '24']);

        $this->setInput("s\nAge\ndesc\nq\n"); // Sort by Age descending, then quit

        $interaction_desc = new TerminalInteraction($table_desc, 5, $this->input_stream, $this->output_stream, null);
        $interaction_desc->run();

        $output = $this->getOutput();
        $this->assertStringContainsString('Table sorted by \'Age\' in descending order.', $output);

        // Extract the last rendered table for order verification
        $last_table_header_start = strrpos($output, '|  Name   | Age |');
        $relevant_output = substr($output, $last_table_header_start);

        $charlie_pos = strpos($relevant_output, 'Charlie');
        $alice_pos = strpos($relevant_output, 'Alice');
        $bob_pos = strpos($relevant_output, 'Bob');

        $this->assertTrue($charlie_pos < $alice_pos);
        $this->assertTrue($alice_pos < $bob_pos);
    }

    public function testSortTableFgetsReturnsFalseForColumnName()
    {
        $table = new Table(['Name', 'Age']);
        $table->addRow(['Alice', '30']);

        $this->setInput("s\n"); // Enter 's', then simulate false for column name input
        $interaction = new TerminalInteraction($table, 5, $this->input_stream, $this->output_stream, null);
        $interaction->run();

        $output = $this->getOutput();
        $this->assertStringContainsString('Enter the column name to sort by:', $output);
        $this->assertStringNotContainsString('Table sorted by', $output);
    }

    public function testSortTableInvalidColumnName()
    {
        $table = new Table(['Name', 'Age']);
        $table->addRow(['Alice', '30']);

        $this->setInput("s\nInvalidColumn\nq\n"); // Enter 's', then invalid column name, then quit
        $interaction = new TerminalInteraction($table, 5, $this->input_stream, $this->output_stream, null);
        $interaction->run();

        $output = $this->getOutput();
        $this->assertStringContainsString('Invalid column name.', $output);
        $this->assertStringNotContainsString('Table sorted by', $output);
    }

    public function testSortTableFgetsReturnsFalseForSortOrder()
    {
        $table = new Table(['Name', 'Age']);
        $table->addRow(['Alice', '30']);

        $this->setInput("s\nName\nq\n"); // Enter 's', then valid column name, then simulate false for sort order input, then quit
        $interaction = new TerminalInteraction($table, 5, $this->input_stream, $this->output_stream, null);
        $interaction->run();

        $output = $this->getOutput();
        $this->assertStringContainsString('Enter sort order (asc/desc, default asc):', $output);
        $this->assertStringContainsString('Table sorted by \'Name\' in ascending order.', $output);
    }

    public function testSortTableInvalidSortOrderDefaultsToAsc()
    {
        $table = new Table(['Name', 'Age']);
        $table->addRow(['Alice', '30']);

        $this->setInput("s\nName\ninvalid\nq\n"); // Enter 's', then valid column name, then invalid sort order, then quit
        $interaction = new TerminalInteraction($table, 5, $this->input_stream, $this->output_stream, null);
        $interaction->run();

        $output = $this->getOutput();
                $this->assertStringContainsString('Table sorted by \'Name\' in ascending order.', $output);
            }
        
            public function testSortTableFgetsReturnsFalseForSortOrderInput()
            {
                $table = new Table(['Name', 'Age']);
                $table->addRow(['Alice', '30']);
        
                $this->setInput("s\nName\n"); // Enter 's', then valid column name, then simulate false for sort order input
                $interaction = new TerminalInteraction($table, 5, $this->input_stream, $this->output_stream, null);
                $interaction->run();
        
                $output = $this->getOutput();
                $this->assertStringContainsString('Enter sort order (asc/desc, default asc):', $output);
                $this->assertStringNotContainsString('Table sorted by', $output);
            }
        }
