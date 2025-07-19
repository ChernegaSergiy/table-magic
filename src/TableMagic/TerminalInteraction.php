<?php

namespace ChernegaSergiy\TableMagic;

class TerminalInteraction
{
    private Table $table;

    private int $current_page = 1;

    private int $rows_per_page;

    /** @var array<int, int> */
    private array $col_widths = [];

    /**
     * TerminalInteraction constructor.
     *
     * @param  Table  $table  The table to be displayed and interacted with.
     * @param  int  $rows_per_page  The number of rows to display per page (default is 5).
     */
    public function __construct(Table $table, int $rows_per_page = 5)
    {
        $this->table = $table;
        $this->rows_per_page = $rows_per_page;
        $this->col_widths = $table->col_widths;
    }

    /**
     * Runs the terminal interaction for paging through the table.
     */
    public function run() : void
    {
        while (true) {
            $this->displayCurrentPage();
            echo "Page {$this->current_page} of {$this->getTotalPages()}\n";
            echo "Enter 'n' for next page, 'p' for previous page, a page number, 'e' to edit a row, 'a' to add a row, 'd' to delete a row, or 'q' to quit: ";

            $input = fgets(STDIN);
            if (false === $input) {
                continue;
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
        $rows_to_display = array_slice($this->table->rows, $start, $this->rows_per_page);
        $current_page_table = new Table($this->table->headers);
        $current_page_table->addRows($rows_to_display);

        foreach ($current_page_table->headers as $index => $header) {
            $current_page_table->col_widths[$index] = $this->col_widths[$index] ?? 0;
        }

        echo $current_page_table->getTable();
    }

    /**
     * Returns the total number of pages.
     *
     * @return int The total number of pages.
     */
    private function getTotalPages() : int
    {
        return (int) ceil(count($this->table->rows) / $this->rows_per_page);
    }

    /**
     * Edits an existing row in the table.
     */
    private function editRow() : void
    {
        echo 'Enter the row number to edit (1 to ' . count($this->table->rows) . '): ';
        $row_number_input = fgets(STDIN);
        if (false === $row_number_input) {
            return;
        }
        $row_number = (int) trim($row_number_input);

        if ($row_number < 1 || $row_number > count($this->table->rows)) {
            echo "Invalid row number.\n";

            return;
        }

        $row_index = $row_number - 1;
        if (! isset($this->table->rows[$row_index])) {
            echo "Row not found.\n";

            return;
        }
        $row = $this->table->rows[$row_index];

        echo 'Editing row ' . $row_number . ': ' . implode(', ', $row) . "\n";

        foreach ($this->table->headers as $index => $header) {
            echo "Enter new value for '{$header}' (leave blank to keep current value): ";
            $new_value = fgets(STDIN);
            if (false === $new_value) {
                continue;
            }
            $new_value = trim($new_value);
            if ('' !== $new_value) {
                $row[$index] = $new_value;
            }
        }

        $this->table->rows[$row_index] = $row;
        $this->updateColumnWidths();
        echo "Row updated successfully.\n";
    }

    /**
     * Adds a new row to the table.
     */
    private function addRow() : void
    {
        $new_row = [];
        foreach ($this->table->headers as $header) {
            echo "Enter value for '{$header}': ";
            $value = fgets(STDIN);
            if (false === $value) {
                continue;
            }
            $new_row[] = trim($value);
        }

        $this->table->addRow($new_row);
        $this->updateColumnWidths();
        echo "Row added successfully.\n";
    }

    /**
     * Deletes an existing row from the table.
     */
    private function deleteRow() : void
    {
        echo 'Enter the row number to delete (1 to ' . count($this->table->rows) . '): ';
        $row_number_input = fgets(STDIN);
        if (false === $row_number_input) {
            return;
        }
        $row_number = (int) trim($row_number_input);

        if ($row_number < 1 || $row_number > count($this->table->rows)) {
            echo "Invalid row number.\n";

            return;
        }

        $row_index = $row_number - 1;
        array_splice($this->table->rows, $row_index, 1);
        $this->updateColumnWidths();
        echo "Row deleted successfully.\n";
    }

    /**
     * Updates the widths of the columns based on the current data in the table.
     */
    private function updateColumnWidths() : void
    {
        $calculate_width = function (string $text): int {
            return mb_strwidth($text, 'UTF-8');
        };
        $this->col_widths = array_map($calculate_width, $this->table->headers);

        foreach ($this->table->rows as $row) {
            foreach ($row as $index => $value) {
                $this->col_widths[$index] = max($this->col_widths[$index] ?? 0, $calculate_width($value));
            }
        }
    }
}
