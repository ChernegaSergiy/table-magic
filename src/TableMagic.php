<?php

namespace ChernegaSergiy\TableMagic;

use Exception;

class TableMagic
{
    protected array $headers = [];

    protected array $rows = [];

    protected array $colWidths = [];

    protected array $alignments = [];

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
     */
    public function addRow(array $row) : void
    {
        $headerCount = count($this->headers);
        $row = array_pad(array_slice($row, 0, $headerCount), $headerCount, '');
        $this->rows[] = $row;
        $this->updateColWidths($row);
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
        $newColumnIndex = count($this->headers);
        $this->headers[] = $header;
        $this->setAlignment($header, $alignment);

        $newColumnWidth = mb_strwidth($header, 'UTF-8');
        foreach ($values as $value) {
            $newColumnWidth = max($newColumnWidth, mb_strwidth((string) $value, 'UTF-8'));
        }
        $this->colWidths[$newColumnIndex] = $newColumnWidth;

        foreach ($this->rows as $index => &$row) {
            $row[] = $values[$index] ?? '';
        }
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

        foreach ($this->rows as $row) {
            $table .= $this->formatRow($row);
        }

        $table .= $this->drawLine();

        return $table;
    }

    protected function formatRow(array $row, int $padType = STR_PAD_RIGHT) : string
    {
        return '|' . implode('|', array_map(function ($value, $i) use ($padType) {
            $align = $this->alignments[$i] ?? 'l';
            $padType = 'r' == $align ? STR_PAD_LEFT : ('c' == $align ? STR_PAD_BOTH : $padType);

            return ' ' . $this->mbStrPad((string) $value, $this->colWidths[$i], ' ', $padType) . ' ';
        }, $row, array_keys($this->headers))) . '|
';
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
     * Sets the alignment for each column header.
     *
     * @param  array  $alignments  An associative array of column headers to their alignment ('l' for left, 'r' for right, 'c' for center).
     */
    public function setAlignments(array $alignments) : void
    {
        foreach ($this->headers as $index => $header) {
            $this->alignments[$index] = $alignments[$header] ?? 'l';
            $this->alignments[$index] = in_array(strtolower($this->alignments[$index]), ['l', 'r', 'c'])
                ? strtolower($this->alignments[$index])
                : 'l';
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

    protected function updateColWidths(array $data) : void
    {
        foreach ($data as $i => $value) {
            $len = mb_strwidth((string) $value, 'UTF-8');
            $this->colWidths[$i] = max($this->colWidths[$i] ?? 0, $len);
        }
    }

    protected function drawLine() : string
    {
        return '+' . implode('+', array_map(fn ($width) => str_repeat('-', $width + 2), $this->colWidths)) . '+
';
    }

    protected function mbStrPad(string $input, int $padLength, string $padString = ' ', int $padType = STR_PAD_RIGHT) : string
    {
        $diff = strlen($input) - mb_strwidth($input, 'UTF-8');

        return str_pad($input, $padLength + $diff, $padString, $padType);
    }

    public function __toString() : string
    {
        return $this->getTable();
    }
}
