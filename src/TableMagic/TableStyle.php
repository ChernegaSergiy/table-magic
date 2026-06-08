<?php

namespace ChernegaSergiy\TableMagic;

class TableStyle
{
    private string $top_left;
    private string $top_horizontal;
    private string $top_intersection;
    private string $top_right;

    private string $header_left;
    private string $header_horizontal;
    private string $header_intersection;
    private string $header_right;

    private string $row_left;
    private string $row_horizontal;
    private string $row_intersection;
    private string $row_right;

    private string $bottom_left;
    private string $bottom_horizontal;
    private string $bottom_intersection;
    private string $bottom_right;

    private string $vertical;

    private bool $has_top_border;
    private bool $has_row_separator;
    private bool $has_bottom_border;

    /**
     * TableStyle constructor.
     *
     * @param string $vertical
     * @param array<int, string> $top
     * @param array{0: string, 1: string, 2: string, 3: string} $header
     * @param array<int, string> $row
     * @param array<int, string> $bottom
     */
    public function __construct(
        string $vertical,
        array $top,
        array $header,
        array $row,
        array $bottom
    ) {
        $this->vertical = $vertical;

        $this->has_top_border = !empty($top);
        $this->top_left = $top[0] ?? '';
        $this->top_horizontal = $top[1] ?? '';
        $this->top_intersection = $top[2] ?? '';
        $this->top_right = $top[3] ?? '';

        // Header separator is mandatory
        [$this->header_left, $this->header_horizontal, $this->header_intersection, $this->header_right] = $header;

        $this->has_row_separator = !empty($row);
        $this->row_left = $row[0] ?? '';
        $this->row_horizontal = $row[1] ?? '';
        $this->row_intersection = $row[2] ?? '';
        $this->row_right = $row[3] ?? '';

        $this->has_bottom_border = !empty($bottom);
        $this->bottom_left = $bottom[0] ?? '';
        $this->bottom_horizontal = $bottom[1] ?? '';
        $this->bottom_intersection = $bottom[2] ?? '';
        $this->bottom_right = $bottom[3] ?? '';
    }

    public function getVertical(): string
    {
        return $this->vertical;
    }

    public function getTopLeft(): string
    {
        return $this->top_left;
    }

    public function getTopHorizontal(): string
    {
        return $this->top_horizontal;
    }

    public function getTopIntersection(): string
    {
        return $this->top_intersection;
    }

    public function getTopRight(): string
    {
        return $this->top_right;
    }

    public function getHeaderLeft(): string
    {
        return $this->header_left;
    }

    public function getHeaderHorizontal(): string
    {
        return $this->header_horizontal;
    }

    public function getHeaderIntersection(): string
    {
        return $this->header_intersection;
    }

    public function getHeaderRight(): string
    {
        return $this->header_right;
    }

    public function getRowLeft(): string
    {
        return $this->row_left;
    }

    public function getRowHorizontal(): string
    {
        return $this->row_horizontal;
    }

    public function getRowIntersection(): string
    {
        return $this->row_intersection;
    }

    public function getRowRight(): string
    {
        return $this->row_right;
    }

    public function getBottomLeft(): string
    {
        return $this->bottom_left;
    }

    public function getBottomHorizontal(): string
    {
        return $this->bottom_horizontal;
    }

    public function getBottomIntersection(): string
    {
        return $this->bottom_intersection;
    }

    public function getBottomRight(): string
    {
        return $this->bottom_right;
    }

    public function hasTopBorder(): bool
    {
        return $this->has_top_border;
    }

    public function hasRowSeparator(): bool
    {
        return $this->has_row_separator;
    }

    public function hasBottomBorder(): bool
    {
        return $this->has_bottom_border;
    }
}
