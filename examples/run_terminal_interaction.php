<?php

require_once __DIR__ . '/../vendor/autoload.php';

use ChernegaSergiy\TableMagic\Table;
use ChernegaSergiy\TableMagic\TerminalInteraction;

// Create a new Table instance
$table = new Table(['Name', 'Age', 'City']);

// Add some initial data
$table->addRow(['Alice', 30, 'New York']);
$table->addRow(['Bob', 24, 'London']);
$table->addRow(['Charlie', 35, 'Paris']);
$table->addRow(['David', 29, 'Berlin']);
$table->addRow(['Eve', 42, 'Rome']);

// Create a TerminalInteraction instance
// The second argument is rows_per_page, set to 3 for demonstration
$interaction = new TerminalInteraction($table, 3);

// Run the terminal interaction
$interaction->run();

echo "\nExited Terminal Interaction.\n";

