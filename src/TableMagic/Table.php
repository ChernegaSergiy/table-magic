<?php

namespace ChernegaSergiy\TableMagic;

use Exception;

class Table
{
    public array $headers = [];

    public array $rows = [];

    private array $dividers = [];

    public array $colWidths = [];

    private array $alignments = [];

    /**
     * Table constructor.
     *
     * @param  array  $headers  Initial headers for the table.
     * @param  array  $alignments  Initial alignments for the columns.
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
     * @param  array  $row  An array representing the row to be added.
     * @param  bool  $divider  Whether to add a divider after this row.
     */
    public function addRow(array $row, bool $divider = false) : void
    {
        $headerCount = count($this->headers);
        $row = array_pad(array_slice($row, 0, $headerCount), $headerCount, '');
        $this->rows[] = $row;
        $this->dividers[] = $divider;
        $this->updateColWidths($row);
    }

    /**
     * Adds multiple rows to the table.
     *
     * @param  array  $rows  An array of arrays representing the rows to be added.
     * @param  array|null  $dividers  An optional array of booleans indicating where dividers should be added.
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
     * @param  array  $values  The values for the new column.
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
     * @param  int  $rowIndex  The index of the row after which the divider should be removed.
     *
     * @throws Exception If the row index is invalid.
     */
    public function removeDivider(int $rowIndex) : void
    {
        if (! isset($this->dividers[$rowIndex])) {
            throw new Exception("Row index $rowIndex is invalid.");
        }

        $this->dividers[$rowIndex] = false;
    }

    /**
     * Checks if a divider exists for the specified row.
     *
     * @param  int  $rowIndex  The index of the row to check for a divider.
     * @return bool Returns `true` if a divider exists for the given row, or `false` if it does not exist or the index is not found.
     */
    public function hasDivider(int $rowIndex) : bool
    {
        return $this->dividers[$rowIndex] ?? false;
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
     * @param  array  $alignments  An associative array of column headers to their alignment ('l' for left, 'r' for right, 'c' for center).
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
            $comparison = is_numeric($a[$index]) && is_numeric($b[$index])
                ? $a[$index] <=> $b[$index]
                : strcmp($a[$index], $b[$index]);

            return 'asc' === $order ? $comparison : -$comparison;
        });
    }

    /**
     * Formats a single row with proper padding and alignment.
     *
     * @param  array  $row  The row to format.
     * @param  int  $padType  The type of padding to use (default is STR_PAD_RIGHT).
     * @return string The formatted row as a string.
     */
    protected function formatRow(array $row, int $padType = STR_PAD_RIGHT) : string
    {
        return '|' . implode('|', array_map(function ($value, $i) use ($padType) {
            $align = $this->alignments[$i] ?? 'l';
            $padType = 'r' === $align ? STR_PAD_LEFT : ('c' === $align ? STR_PAD_BOTH : $padType);

            return ' ' . $this->mbStrPad((string) $value, $this->colWidths[$i], ' ', $padType) . ' ';
        }, $row, array_keys($this->headers))) . '|' . PHP_EOL;
    }

    /**
     * Updates the width of each column based on the provided data.
     *
     * @param  array  $data  The data to evaluate for column widths.
     */
    protected function updateColWidths(array $data) : void
    {
        foreach ($data as $i => $value) {
            $len = mb_strwidth((string) $value, 'UTF-8');
            $this->colWidths[$i] = max($this->colWidths[$i] ?? 0, $len);
        }
    }

    /**
     * Draws a horizontal line based on the column widths.
     *
     * @return string The drawn line as a string.
     */
    protected function drawLine() : string
    {
        return '+' . implode('+', array_map(fn ($width) => str_repeat('-', $width + 2), $this->colWidths)) . '+' . PHP_EOL;
    }

    /**
     * Pads a string to a specified length, accounting for multibyte characters.
     *
     * @param  string  $input  The input string to pad.
     * @param  int  $padLength  The length to pad to.
     * @param  string  $padString  The string to pad with (default is a space).
     * @param  int  $padType  The type of padding to use (default is STR_PAD_RIGHT).
     * @return string The padded string.
     */
    protected function mbStrPad(string $input, int $padLength, string $padString = ' ', int $padType = STR_PAD_RIGHT) : string
    {
        $diff = strlen($input) - mb_strwidth($input, 'UTF-8');

        return str_pad($input, $padLength + $diff, $padString, $padType);
    }

    /**
     * Updates the width of a specific column based on its header and values.
     *
     * @param  string  $header  The header of the column.
     * @param  array  $values  The values of the column.
     */
    protected function updateColumnWidth(string $header, array $values) : void
    {
        $newColumnWidth = mb_strwidth($header, 'UTF-8');
        foreach ($values as $value) {
            $newColumnWidth = max($newColumnWidth, mb_strwidth((string) $value, 'UTF-8'));
        }
        $this->colWidths[] = $newColumnWidth;
    }

    /**
     * Appends values to the end of each row for a new column.
     *
     * @param  array  $values  The values to append.
     */
    protected function appendColumnValues(array $values) : void
    {
        foreach ($this->rows as $index => &$row) {
            $row[] = $values[$index] ?? '';
        }
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
