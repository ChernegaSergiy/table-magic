<?php

namespace ChernegaSergiy\TableMagic;

class TerminalInteraction
{
    private Table $table;

    private int $current_page = 1;

    private int $rows_per_page;



    /** @var resource */
    private $input_stream;

    /** @var resource */
    private $output_stream;

    /**
     * TerminalInteraction constructor.
     *
     * @param  Table  $table  The table to be displayed and interacted with.
     * @param  int  $rows_per_page  The number of rows to display per page (default is 5).
     * @param  resource  $input_stream  The input stream to read from (default is STDIN).
     * @param  resource  $output_stream  The output stream to write to (default is STDOUT).
     */
    public function __construct(Table $table, int $rows_per_page = 5, $input_stream = STDIN, $output_stream = STDOUT)
    {
        $this->table = $table;
        $this->rows_per_page = $rows_per_page;

        $this->input_stream = $input_stream;
        $this->output_stream = $output_stream;
    }

    /**
     * Runs the terminal interaction for paging through the table.
     */
    public function run() : void
    {
        while (true) {
            $this->displayCurrentPage();
            fwrite($this->output_stream, "Page {$this->current_page} of {$this->getTotalPages()}\n");
            fwrite($this->output_stream, "Enter 'n' for next page, 'p' for previous page, a page number, 'e' to edit a row, 'a' to add a row, 'd' to delete a row, or 'q' to quit: ");

            $input = fgets($this->input_stream);
            if (false === $input) {
                break;
            }
            $input = trim($input);

            if ('q' === $input) {
                break;
            } elseif ('n' === $input && $this->current_page < $this->getTotalPages()) {
                $this->current_page++;
            } elseif ('p' === $input && $this->current_page > 1) {
                $this->current_page--;
            } elseif (is_numeric($input) && $input > 0 && $input <= $this->getTotalPages()) {
                $this->current_page = (int) $input;
            } elseif ('e' === $input) {
                $this->editRow();
            } elseif ('a' === $input) {
                $this->addRow();
            } elseif ('d' === $input) {
                $this->deleteRow();
            }
        }
    }

    /**
     * Displays the current page of the table.
     */
    private function displayCurrentPage() : void
    {
        $start = ($this->current_page - 1) * $this->rows_per_page;
        /** @var array<int, array<int, string>> $rows_to_display */
        $rows_to_display = array_slice($this->table->getRows(), $start, $this->rows_per_page);
        $current_page_table = new Table($this->table->headers);
        $current_page_table->addRows($rows_to_display);



        fwrite($this->output_stream, $current_page_table->getTable());
    }

    /**
     * Returns the total number of pages.
     *
     * @return int The total number of pages.
     */
    private function getTotalPages() : int
    {
        return (int) ceil($this->table->getRowCount() / $this->rows_per_page);
    }

    /**
     * Edits an existing row in the table.
     */
    private function editRow() : void
    {
        fwrite($this->output_stream, 'Enter the row number to edit (1 to ' . $this->table->getRowCount() . '): ');
        $row_number_input = fgets($this->input_stream);

        if (false === $row_number_input) {
            return;
        }

        $row_number = (int) trim($row_number_input);

        if ($row_number < 1 || $row_number > $this->table->getRowCount()) {
            fwrite($this->output_stream, "Invalid row number.\n");
            return;
        }

        $row_index = $row_number - 1;
        $row = $this->table->getRow($row_index);
        $original_row = $row;

        fwrite($this->output_stream, 'Editing row ' . $row_number . ': ' . implode(', ', $row) . "\n");

        $changes_made = false;

        foreach ($this->table->headers as $index => $header) {
            fwrite($this->output_stream, "Enter new value for '{$header}' (leave blank to keep current value): ");
            $new_value = fgets($this->input_stream);

            if (false === $new_value) {
                continue;
            }

            $new_value = trim($new_value);

            if ('' !== $new_value) {
                /** @var array<int, string> $temp_row */
                $temp_row = $this->table->getRow($row_index);
                $temp_row[$index] = $new_value;

                $this->table->updateRow($row_index, $temp_row);

                $row = $this->table->getRow($row_index);
                $changes_made = true;
            }
        }

        if ($changes_made) {
            fwrite($this->output_stream, "Row updated successfully.\n");
        } else {
            fwrite($this->output_stream, "No changes made to row.\n");
        }
    }

    /**
     * Adds a new row to the table.
     */
    private function addRow() : void
    {
        $new_row = [];
        foreach ($this->table->headers as $header) {
            fwrite($this->output_stream, "Enter value for '{$header}': ");
            $value = fgets($this->input_stream);
            if (false === $value) {
                return;
            }
            $new_row[] = trim($value);
        }

        $this->table->addRow($new_row);
        fwrite($this->output_stream, "Row added successfully.\n");
    }

    /**
     * Deletes an existing row from the table.
     */
    private function deleteRow() : void
    {
        fwrite($this->output_stream, 'Enter the row number to delete (1 to ' . $this->table->getRowCount() . '): ');
        $row_number_input = fgets($this->input_stream);
        if (false === $row_number_input) {
            return;
        }
        $row_number = (int) trim($row_number_input);

        if ($row_number < 1 || $row_number > $this->table->getRowCount()) {
            fwrite($this->output_stream, "Invalid row number.\n");

            return;
        }

        $row_index = $row_number - 1;
        $this->table->deleteRow($row_index);

        fwrite($this->output_stream, "Row deleted successfully.\n");
    }
}
