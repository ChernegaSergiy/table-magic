<?php

namespace ChernegaSergiy\TableMagic;

class TerminalInteraction
{
    private Table $table;

    private int $currentPage = 1;

    private int $rowsPerPage;

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
    }

    /**
     * Runs the terminal interaction for paging through the table.
     */
    public function run() : void
    {
        while (true) {
            $this->displayCurrentPage();
            echo "Page {$this->currentPage} of {$this->getTotalPages()}\n";
            echo "Enter 'n' for next page, 'p' for previous page, a page number, or 'q' to quit: ";

            $input = trim(fgets(STDIN));

            if ('q' === $input) {
                break;
            } elseif ('n' === $input && $this->currentPage < $this->getTotalPages()) {
                $this->currentPage++;
            } elseif ('p' === $input && $this->currentPage > 1) {
                $this->currentPage--;
            } elseif (is_numeric($input) && $input > 0 && $input <= $this->getTotalPages()) {
                $this->currentPage = (int) $input;
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
}
