<?php

namespace ChernegaSergiy\TableMagic;

use Exception;

class Table
{
    /** @var array<int, string> */
    public array $headers = [];

    /** @var array<int, array<int, string>> */
    private array $rows = [];

    /** @var array<int, bool> */
    private array $dividers = [];

    /** @var array<int, int> */
    public array $col_widths = [];

    /**
     * Returns the column widths.
     *
     * @return array<int, int> The column widths.
     */
    public function getColWidths() : array
    {
        return $this->col_widths;
    }

    /** @var array<int, string> */
    private array $alignments = [];

    /**
     * Table constructor.
     *
     * @param  array<int, string>  $headers  Initial headers for the table.
     * @param  array<string, string>  $alignments  Initial alignments for the columns.
     */
    public function __construct(array $headers = [], array $alignments = [])
    {
        $this->headers = $headers;
        $this->setAlignments($alignments);
        $this->updateColWidths($headers);
    }

    /**
     * Adds a new row to the table.
     *
     * @param  array<int|string, string>  $row  An array representing the row to be added.
     * @param  bool  $divider  Whether to add a divider after this row.
     */
    public function addRow(array $row, bool $divider = false) : void
    {
        $header_count = count($this->headers);
        $row = array_pad(array_slice($row, 0, $header_count), $header_count, '');
        $this->rows[] = array_values($row);
        $this->dividers[] = $divider;
        $this->updateColWidths(array_values($row));
    }

    /**
     * Adds multiple rows to the table.
     *
     * @param  array<int, array<int|string, string>>  $rows  An array of arrays representing the rows to be added.
     * @param  array<int, bool>|null  $dividers  An optional array of booleans indicating where dividers should be added.
     */
    public function addRows(array $rows, ?array $dividers = null) : void
    {
        foreach ($rows as $index => $row) {
            $divider = $dividers[$index] ?? false;
            $this->addRow($row, $divider);
        }
    }

    /**
     * Adds a new column to the table.
     *
     * @param  string  $header  The header for the new column.
     * @param  array<int, string>  $values  The values for the new column.
     * @param  string  $alignment  The alignment for the new column ('l', 'r', or 'c').
     */
    public function addColumn(string $header, array $values, string $alignment = 'l') : void
    {
        $this->headers[] = $header;
        $this->setAlignment($header, $alignment);
        $this->updateColumnWidth($header, $values);
        $this->appendColumnValues($values);
    }

    /**
     * Removes the divider after the specified row index.
     *
     * @param  int  $row_index  The index of the row after which the divider should be removed.
     *
     * @throws Exception If the row index is invalid.
     */
    public function removeDivider(int $row_index) : void
    {
        if (! isset($this->dividers[$row_index])) {
            throw new Exception("Row index $row_index is invalid.");
        }

        $this->dividers[$row_index] = false;
    }

    /**
     * Checks if a divider exists for the specified row.
     *
     * @param  int  $row_index  The index of the row to check for a divider.
     * @return bool Returns `true` if a divider exists for the given row, or `false` if it does not exist or the index is not found.
     */
    public function hasDivider(int $row_index) : bool
    {
        return (bool) ($this->dividers[$row_index] ?? false);
    }

    /**
     * Returns the formatted table as a string.
     *
     * @return string The formatted table.
     */
    public function getTable() : string
    {
        if (empty($this->headers)) {
            return 'Empty table';
        }

        $table = $this->drawLine();
        $table .= $this->formatRow($this->headers, STR_PAD_BOTH);
        $table .= $this->drawLine();

        foreach ($this->rows as $i => $row) {
            $table .= $this->formatRow($row);
            if ($this->dividers[$i] ?? false) {
                $table .= $this->drawLine();
            }
        }

        $table .= $this->drawLine();

        return $table;
    }

    /**
     * Sets the alignment for each column header.
     *
     * @param  array<string, string>  $alignments  An associative array of column headers to their alignment ('l' for left, 'r' for right, 'c' for center).
     */
    public function setAlignments(array $alignments) : void
    {
        foreach ($this->headers as $index => $header) {
            $this->alignments[$index] = $alignments[$header] ?? 'l';
        }
    }

    /**
     * Sets the alignment for a specific column.
     *
     * @param  string  $column  The name of the column to set the alignment for.
     * @param  string  $alignment  The alignment to set ('l' for left, 'r' for right, 'c' for center).
     *
     * @throws Exception If the column is not found.
     */
    public function setAlignment(string $column, string $alignment) : void
    {
        $index = array_search($column, $this->headers);

        if (false === $index) {
            $index = count($this->headers) - 1;
        }

        $alignment = strtolower($alignment);
        $this->alignments[$index] = in_array($alignment, ['l', 'r', 'c']) ? $alignment : 'l';
    }

    /**
     * Sorts the table by a specific column.
     *
     * @param  string  $column  The name of the column to sort by.
     * @param  string  $order  The order to sort ('asc' for ascending, 'desc' for descending).
     *
     * @throws Exception If the column is not found.
     */
    public function sortTable(string $column, string $order = 'asc') : void
    {
        $index = array_search($column, $this->headers);

        if (false === $index) {
            throw new Exception("Column '$column' not found.");
        }

        $order = 'desc' === strtolower($order) ? 'desc' : 'asc';

        usort($this->rows, function ($a, $b) use ($index, $order) {
            $val_a = (string) ($a[$index] ?? '');
            $val_b = (string) ($b[$index] ?? '');
            $comparison = is_numeric($val_a) && is_numeric($val_b)
                ? $val_a <=> $val_b
                : strcmp($val_a, $val_b);

            return 'asc' === $order ? $comparison : -$comparison;
        });
    }

    /**
     * Formats a single row with proper padding and alignment.
     *
     * @param  array<int, string>  $row  The row to format.
     * @param  int  $pad_type  The type of padding to use (default is STR_PAD_RIGHT).
     * @return string The formatted row as a string.
     */
    protected function formatRow(array $row, int $pad_type = STR_PAD_RIGHT) : string
    {
        return '|' . implode('|', array_map(function ($value, $i) use ($pad_type) {
            $align = $this->alignments[$i] ?? 'l';
            $pad_type = 'r' === $align ? STR_PAD_LEFT : ('c' === $align ? STR_PAD_BOTH : $pad_type);

            return ' ' . $this->mbStrPad($value, $this->col_widths[$i] ?? 0, ' ', $pad_type) . ' ';
        }, $row, array_keys($this->headers))) . '|' . PHP_EOL;
    }

    /**
     * Updates the width of each column based on the provided data.
     *
     * @param  array<int, string>  $data  The data to evaluate for column widths.
     */
    public function updateColWidths(array $data) : void
    {
        foreach ($data as $i => $value) {
            $len = mb_strwidth($value, 'UTF-8');
            $this->col_widths[$i] = max($this->col_widths[$i] ?? 0, $len);
        }
    }

    /**
     * Draws a horizontal line based on the column widths.
     *
     * @return string The drawn line as a string.
     */
    protected function drawLine() : string
    {
        return '+' . implode('+', array_map(fn ($width) => str_repeat('-', $width + 2), $this->col_widths)) . '+' . PHP_EOL;
    }

    /**
     * Pads a string to a specified length, accounting for multibyte characters.
     *
     * @param  string  $input  The input string to pad.
     * @param  int  $pad_length  The length to pad to.
     * @param  string  $pad_string  The string to pad with (default is a space).
     * @param  int  $pad_type  The type of padding to use (default is STR_PAD_RIGHT).
     * @return string The padded string.
     */
    protected function mbStrPad(string $input, int $pad_length, string $pad_string = ' ', int $pad_type = STR_PAD_RIGHT) : string
    {
        $diff = strlen($input) - mb_strwidth($input, 'UTF-8');

        return str_pad($input, $pad_length + $diff, $pad_string, $pad_type);
    }

    /**
     * Updates the width of a specific column based on its header and values.
     *
     * @param  string  $header  The header of the column.
     * @param  array<int, string>  $values  The values of the column.
     */
    protected function updateColumnWidth(string $header, array $values) : void
    {
        $new_column_width = mb_strwidth($header, 'UTF-8');
        foreach ($values as $value) {
            $new_column_width = max($new_column_width, mb_strwidth($value, 'UTF-8'));
        }
        $this->col_widths[] = $new_column_width;
    }

    /**
     * Appends values to the end of each row for a new column.
     *
     * @param  array<int, string>  $values  The values to append.
     */
    protected function appendColumnValues(array $values) : void
    {
        foreach ($this->rows as $index => &$row) {
            $row[] = $values[$index] ?? '';
        }
    }

    /**
     * Returns all rows in the table.
     *
     * @return array<int, array<int, string>> The rows of the table.
     */
    public function getRows() : array
    {
        return $this->rows;
    }

    /**
     * Returns a specific row from the table.
     *
     * @param int $index The index of the row to retrieve.
     * @return array<int, string> The row at the specified index.
     * @throws Exception If the row index is invalid.
     */
    public function getRow(int $index) : array
    {
        if (! isset($this->rows[$index])) {
            throw new Exception("Row index $index is invalid.");
        }
        return $this->rows[$index];
    }

    /**
     * Updates a specific row in the table.
     *
     * @param int $index The index of the row to update.
     * @param array<int|string, string> $newRow The new data for the row.
     * @throws Exception If the row index is invalid.
     */
    public function updateRow(int $index, array $newRow) : void
    {
        if (! isset($this->rows[$index])) {
            throw new Exception("Row index $index is invalid.");
        }
        $header_count = count($this->headers);
        $newRow = array_pad(array_slice($newRow, 0, $header_count), $header_count, '');
        $this->rows[$index] = array_values($newRow);
        $this->updateColWidths(array_values($newRow));
    }

    /**
     * Returns the total number of rows in the table.
     *
     * @return int The number of rows.
     */
    public function getRowCount() : int
    {
        return count($this->rows);
    }

    /**
     * Deletes a specific row from the table.
     *
     * @param int $index The index of the row to delete.
     * @throws Exception If the row index is invalid.
     */
    public function deleteRow(int $index) : void
    {
        if (! isset($this->rows[$index])) {
            throw new Exception("Row index $index is invalid.");
        }
        array_splice($this->rows, $index, 1);
        array_splice($this->dividers, $index, 1);
    }

    /**
     * Returns the string representation of the table.
     *
     * @return string The formatted table.
     */
    public function __toString() : string
    {
        return $this->getTable();
    }
}
