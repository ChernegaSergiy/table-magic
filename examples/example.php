<?php

require_once __DIR__ . '/../../../autoload.php'; // Include Composer autoload

use ChernegaSergiy\TableMagic\Table;
use ChernegaSergiy\TableMagic\TableExporter;
use ChernegaSergiy\TableMagic\TableImporter;

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

// Set alignment for a specific column
$table->setAlignment('Department', 'l'); // Change alignment for Department
$table->setAlignment('Performance Score', 'c'); // Change alignment for Performance Score

echo "\n\nTable with Specific Column Alignments:\n";
echo $table;

// Add more rows to the table
$table->addRow([1006, 'Frank Castle', 'IT', 95, '2024-07-01']);
$table->addRow([1007, 'Grace Johnson', 'Finance', 80, '2024-07-03']);

echo "\n\nTable After Adding More Rows:\n";
echo $table;

// Sort the table by the new column (Department)
$table->sortTable('Department');
echo "\n\nSorted by Department:\n";
echo $table;

// Example of importing data
$dataJson = json_encode([
    'headers' => $headers,
    'rows' => [
        [1008, 'Hannah Smith', 'Marketing', 87, '2024-06-30'],
        [1009, 'Ian Brown', 'Sales', 91, '2024-07-02'],
    ],
]);

// Creating a new importer
$tableImporter = new TableImporter();
$table = $tableImporter->import($dataJson, 'json'); // Import from JSON

// Output of the imported table
echo "\n\nTable After Importing Data from JSON:\n";
echo $table;

// Example of exporting the table to CSV
$tableExporter = new TableExporter($table);
$csvOutput = $tableExporter->export('csv');
file_put_contents('table_output.csv', $csvOutput); // Save to file

echo "\n\nTable exported to CSV file 'table_output.csv'.\n";
