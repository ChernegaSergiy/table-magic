<?php

namespace ChernegaSergiy\TableMagic;

class TableStyle
{
    private string $topLeft;
    private string $topHorizontal;
    private string $topIntersection;
    private string $topRight;

    private string $headerLeft;
    private string $headerHorizontal;
    private string $headerIntersection;
    private string $headerRight;

    private string $rowLeft;
    private string $rowHorizontal;
    private string $rowIntersection;
    private string $rowRight;

    private string $bottomLeft;
    private string $bottomHorizontal;
    private string $bottomIntersection;
    private string $bottomRight;

    private string $vertical;

    private bool $hasTopBorder;
    private bool $hasRowSeparator;
    private bool $hasBottomBorder;

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

        $this->hasTopBorder = !empty($top);
        $this->topLeft = $top[0] ?? '';
        $this->topHorizontal = $top[1] ?? '';
        $this->topIntersection = $top[2] ?? '';
        $this->topRight = $top[3] ?? '';

        // Header separator is mandatory
        [$this->headerLeft, $this->headerHorizontal, $this->headerIntersection, $this->headerRight] = $header;

        $this->hasRowSeparator = !empty($row);
        $this->rowLeft = $row[0] ?? '';
        $this->rowHorizontal = $row[1] ?? '';
        $this->rowIntersection = $row[2] ?? '';
        $this->rowRight = $row[3] ?? '';

        $this->hasBottomBorder = !empty($bottom);
        $this->bottomLeft = $bottom[0] ?? '';
        $this->bottomHorizontal = $bottom[1] ?? '';
        $this->bottomIntersection = $bottom[2] ?? '';
        $this->bottomRight = $bottom[3] ?? '';
    }

    public function getVertical(): string
    {
        return $this->vertical;
    }

    public function getTopLeft(): string
    {
        return $this->topLeft;
    }

    public function getTopHorizontal(): string
    {
        return $this->topHorizontal;
    }

    public function getTopIntersection(): string
    {
        return $this->topIntersection;
    }

    public function getTopRight(): string
    {
        return $this->topRight;
    }

    public function getHeaderLeft(): string
    {
        return $this->headerLeft;
    }

    public function getHeaderHorizontal(): string
    {
        return $this->headerHorizontal;
    }

    public function getHeaderIntersection(): string
    {
        return $this->headerIntersection;
    }

    public function getHeaderRight(): string
    {
        return $this->headerRight;
    }

    public function getRowLeft(): string
    {
        return $this->rowLeft;
    }

    public function getRowHorizontal(): string
    {
        return $this->rowHorizontal;
    }

    public function getRowIntersection(): string
    {
        return $this->rowIntersection;
    }

    public function getRowRight(): string
    {
        return $this->rowRight;
    }

    public function getBottomLeft(): string
    {
        return $this->bottomLeft;
    }

    public function getBottomHorizontal(): string
    {
        return $this->bottomHorizontal;
    }

    public function getBottomIntersection(): string
    {
        return $this->bottomIntersection;
    }

    public function getBottomRight(): string
    {
        return $this->bottomRight;
    }

    public function hasTopBorder(): bool
    {
        return $this->hasTopBorder;
    }

    public function hasRowSeparator(): bool
    {
        return $this->hasRowSeparator;
    }

    public function hasBottomBorder(): bool
    {
        return $this->hasBottomBorder;
    }


}
