<?php

require_once __DIR__ . '/../vendor/autoload.php'; // Include Composer autoload

use ChernegaSergiy\TableMagic\Table;
use ChernegaSergiy\TableMagic\TableExporter;
use ChernegaSergiy\TableMagic\TableImporter;
use ChernegaSergiy\TableMagic\TableStyle;
use ChernegaSergiy\TableMagic\TableStyleRegistry;

// Table headers
$headers = ['Employee ID', 'Name', 'Department', 'Performance Score', 'Review Date'];
$alignments = [
    'Employee ID' => 'r',
    'Performance Score' => 'r',
    'Review Date' => 'c',
];

// Create a table object
$table = new Table($headers, $alignments);

// Add rows to the table
$table->addRow([1001, 'Alice Thompson', 'Marketing', 85, '2024-06-15']);
$table->addRow([1002, 'Brian Lee', 'Sales', 90, '2024-06-18']);
$table->addRow([1003, 'Cathy Kim', 'HR', 78, '2024-06-20']);
$table->addRow([1004, 'David Clark', 'IT', 92, '2024-06-22']);
$table->addRow([1005, 'Eva Adams', 'Finance', 88, '2024-06-25']);

// Display the table
echo "Original Table:\n";
echo $table;

// Sort by Performance Score in ascending order
$table->sortTable('Performance Score');
echo "\n\nSorted by Performance Score (ascending):\n";
echo $table;

// Sort by Name in descending order
$table->sortTable('Name', 'desc');
echo "\n\nSorted by Name (descending):\n";
echo $table;

// Set new alignments for the columns
$table->setAlignments([
    'Employee ID' => 'c',
    'Name' => 'l',
    'Department' => 'c',
    'Performance Score' => 'r',
    'Review Date' => 'c',
]);

echo "\n\nTable with New Alignments:\n";
echo $table;

// Set alignment for specific columns
$table->setAlignment('Department', 'l');
$table->setAlignment('Performance Score', 'c');

echo "\n\nTable with Specific Column Alignments:\n";
echo $table;

// Add more rows
$table->addRow([1006, 'Frank Castle', 'IT', 95, '2024-07-01']);
$table->addRow([1007, 'Grace Johnson', 'Finance', 80, '2024-07-03']);

echo "\n\nTable After Adding More Rows:\n";
echo $table;

// Sort by Department
$table->sortTable('Department');
echo "\n\nSorted by Department:\n";
echo $table;

// Example of importing data
$data_json = json_encode([
    'headers' => $headers,
    'rows' => [
        [1008, 'Hannah Smith', 'Marketing', 87, '2024-06-30'],
        [1009, 'Ian Brown', 'Sales', 91, '2024-07-02'],
    ],
]);

$table_importer = new TableImporter();
$table = $table_importer->import($data_json, 'json');

echo "\n\nTable After Importing Data from JSON:\n";
echo $table;

// Export table to CSV
$table_exporter = new TableExporter($table);
$csv_output = $table_exporter->export('csv');
file_put_contents('table_output.csv', $csv_output);

echo "\n\nTable exported to CSV file 'table_output.csv'.\n";

// --- Style Demonstrations ---
echo "\n\n--- Style Demonstrations ---\n";

// Compact Style
echo "\nCompact Style:\n";
$table->setStyle('compact');
echo $table;

// Default Style
echo "\nDefault Style:\n";
$table->setStyle('default');
echo $table;

// Dots Style
echo "\nDots Style:\n";
$table->setStyle('dots');
echo $table;

// GitHub Markdown Style
echo "\nGitHub Markdown Style:\n";
$table->setStyle('github-markdown');
echo $table;

// Rounded Style
echo "\nRounded Style:\n";
$table->setStyle('rounded');
echo $table;

// Reddit Markdown Style
echo "\nReddit Markdown Style:\n";
$table->setStyle('reddit-markdown');
echo $table;

// reStructuredTextGrid Style
echo "\nreStructuredTextGrid Style:\n";
$table->setStyle('restructured-text-grid');
echo $table;

// reStructuredText Simple Style
echo "\nreStructuredText Simple Style:\n";
$table->setStyle('restructured-text-simple');
echo $table;

// Unicode Double Line Style
echo "\nUnicode Double Line Style:\n";
$table->setStyle('unicode-double-line');
echo $table;

// Unicode Single Line Style
echo "\nUnicode Single Line Style:\n";
$table->setStyle('unicode-single-line');
echo $table;

// Custom 'Ugly Chaos' Style
echo "\nCustom 'Ugly Chaos' Style:\n";
$ugly_chaos_style = new TableStyle(
    '!',
    ['<', '-', '>', '>'],
    ['!', '=', 'X', '!'],
    ['!', '-', ' ', '!'],
    ['<', '-', '>', '>']
);
TableStyleRegistry::register('ugly-chaos', $ugly_chaos_style);
$table->setStyle('ugly-chaos');
echo $table;

