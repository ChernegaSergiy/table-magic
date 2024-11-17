<?php

namespace ChernegaSergiy\TableMagic;

class TerminalInteraction
{
    private Table $table;

    private int $currentPage = 1;

    private int $rowsPerPage;

    private array $colWidths = [];

    /**
     * TerminalInteraction constructor.
     *
     * @param  Table  $table  The table to be displayed and interacted with.
     * @param  int  $rowsPerPage  The number of rows to display per page (default is 5).
     */
    public function __construct(Table $table, int $rowsPerPage = 5)
    {
        $this->table = $table;
        $this->rowsPerPage = $rowsPerPage;
        $this->colWidths = $table->colWidths;
    }

    /**
     * Runs the terminal interaction for paging through the table.
     */
    public function run() : void
    {
        while (true) {
            $this->displayCurrentPage();
            echo "Page {$this->currentPage} of {$this->getTotalPages()}\n";
            echo "Enter 'n' for next page, 'p' for previous page, a page number, 'e' to edit a row, 'a' to add a row, 'd' to delete a row, or 'q' to quit: ";

            $input = trim(fgets(STDIN));

            if ('q' === $input) {
                break;
            } elseif ('n' === $input && $this->currentPage < $this->getTotalPages()) {
                $this->currentPage++;
            } elseif ('p' === $input && $this->currentPage > 1) {
                $this->currentPage--;
            } elseif (is_numeric($input) && $input > 0 && $input <= $this->getTotalPages()) {
                $this->currentPage = (int) $input;
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
        $start = ($this->currentPage - 1) * $this->rowsPerPage;
        $rowsToDisplay = array_slice($this->table->rows, $start, $this->rowsPerPage);
        $currentPageTable = new Table($this->table->headers);
        $currentPageTable->addRows($rowsToDisplay);

        foreach ($currentPageTable->headers as $index => $header) {
            $currentPageTable->colWidths[$index] = $this->colWidths[$index];
        }

        echo $currentPageTable->getTable();
    }

    /**
     * Returns the total number of pages.
     *
     * @return int The total number of pages.
     */
    private function getTotalPages() : int
    {
        return (int) ceil(count($this->table->rows) / $this->rowsPerPage);
    }

    /**
     * Edits an existing row in the table.
     */
    private function editRow() : void
    {
        echo 'Enter the row number to edit (1 to ' . count($this->table->rows) . '): ';
        $rowNumber = (int) trim(fgets(STDIN));

        if ($rowNumber < 1 || $rowNumber > count($this->table->rows)) {
            echo "Invalid row number.\n";

            return;
        }

        $rowIndex = $rowNumber - 1;
        $row = $this->table->rows[$rowIndex];

        echo 'Editing row ' . $rowNumber . ': ' . implode(', ', $row) . "\n";

        foreach ($this->table->headers as $index => $header) {
            echo "Enter new value for '{$header}' (leave blank to keep current value): ";
            $newValue = trim(fgets(STDIN));
            if ('' !== $newValue) {
                $row[$index] = $newValue;
            }
        }

        $this->table->rows[$rowIndex] = $row;
        $this->updateColumnWidths();
        echo "Row updated successfully.\n";
    }

    /**
     * Adds a new row to the table.
     */
    private function addRow() : void
    {
        $newRow = [];
        foreach ($this->table->headers as $header) {
            echo "Enter value for '{$header}': ";
            $value = trim(fgets(STDIN));
            $newRow[] = $value;
        }

        $this->table->addRow($newRow);
        $this->updateColumnWidths();
        echo "Row added successfully.\n";
    }

    /**
     * Deletes an existing row from the table.
     */
    private function deleteRow() : void
    {
        echo 'Enter the row number to delete (1 to ' . count($this->table->rows) . '): ';
        $rowNumber = (int) trim(fgets(STDIN));

        if ($rowNumber < 1 || $rowNumber > count($this->table->rows)) {
            echo "Invalid row number.\n";

            return;
        }

        $rowIndex = $rowNumber - 1;
        array_splice($this->table->rows, $rowIndex, 1);
        $this->updateColumnWidths();
        echo "Row deleted successfully.\n";
    }

    /**
     * Updates the widths of the columns based on the current data in the table.
     */
    private function updateColumnWidths() : void
    {
        $calculateWidth = fn ($text) => mb_strwidth((string) $text, 'UTF-8');
        $this->colWidths = array_map($calculateWidth, $this->table->headers);

        foreach ($this->table->rows as $row) {
            foreach ($row as $index => $value) {
                $this->colWidths[$index] = max($this->colWidths[$index] ?? 0, $calculateWidth($value));
            }
        }
    }
}
