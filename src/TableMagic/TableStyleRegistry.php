<?php

namespace ChernegaSergiy\TableMagic;

use InvalidArgumentException;

class TableStyleRegistry
{
    /** @var array<string, TableStyle> */
    private static array $styles = [];
    private static bool $is_initialized = false;

    private static function init() : void
    {
        if (self::$is_initialized) {
            return;
        }

        self::register('default', new TableStyle(
            '|',
            ['+', '-', '+', '+'],
            ['+', '-', '+', '+'],
            ['+', '-', '+', '+'],
            ['+', '-', '+', '+']
        ));
        self::register('compact', new TableStyle(
            ' ',
            [],
            [' ', '-', ' ', ' '],
            [],
            []
        ));
        self::register('dots', new TableStyle(
            ':',
            ['.', '.', '.', '.'],
            [':', '.', ':', ':'],
            [':', '.', ':', ':'],
            ['.', '.', '.', '.']
        ));
        self::register('rounded', new TableStyle(
            '|',
            ['.', '-', '.', '.'],
            [':', '-', '+', ':'],
            [':', '-', '+', ':'],
            ['\'', '-', '\'', '\'']
        ));

        // === UNICODE STYLES ===
        self::register('unicode-single-line', new TableStyle(
            '│',
            ['┌', '─', '┬', '┐'],
            ['├', '─', '┼', '┤'],
            ['├', '─', '┼', '┤'],
            ['└', '─', '┴', '┘']
        ));
        self::register('unicode-double-line', new TableStyle(
            '║',
            ['╔', '═', '╦', '╗'],
            ['╠', '═', '╬', '╣'],
            ['╠', '═', '╬', '╣'],
            ['╚', '═', '╩', '╝']
        ));

        // === TEXT-BASED / MARKDOWN STYLES ===
        self::register('github-markdown', new TableStyle(
            '|',
            [],
            ['|', '-', '|', '|'],
            [],
            []
        ));
        self::register('reddit-markdown', new TableStyle(
            '|',
            [],
            [' ', '-', '|', ' '],
            [' ', ' ', '|', ' '],
            []
        ));

        // === RESTRUCTURED TEXT STYLES ===
        self::register('restructured-text-grid', new TableStyle(
            '|',
            ['+', '-', '+', '+'],
            ['+', '=', '+', '+'],
            ['+', '-', '+', '+'],
            ['+', '-', '+', '+']
        ));
        self::register('restructured-text-simple', new TableStyle(
            ' ',
            [' ', '=', ' ', ' '],
            [' ', '=', ' ', ' '],
            [' ', '=', ' ', ' '],
            [' ', '=', ' ', ' ']
        ));

        self::$is_initialized = true;
    }

    public static function register(string $name, TableStyle $style) : void
    {
        self::$styles[$name] = $style;
    }

    public static function get(string $name) : TableStyle
    {
        self::init();

        if (!isset(self::$styles[$name])) {
            throw new InvalidArgumentException(sprintf('Style "%s" is not registered.', $name));
        }

        return self::$styles[$name];
    }
}
