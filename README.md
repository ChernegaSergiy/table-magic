# TableMagic

[![Latest Stable Version](https://img.shields.io/packagist/v/chernegasergiy/table-magic.svg?label=Packagist&logo=packagist)](https://packagist.org/packages/chernegasergiy/table-magic)
[![Total Downloads](https://img.shields.io/packagist/dt/chernegasergiy/table-magic.svg?label=Downloads&logo=packagist)](https://packagist.org/packages/chernegasergiy/table-magic)
[![License](https://img.shields.io/packagist/l/chernegasergiy/table-magic.svg?label=Licence&logo=open-source-initiative)](https://packagist.org/packages/chernegasergiy/table-magic)
[![Tests](https://img.shields.io/github/actions/workflow/status/ChernegaSergiy/table-magic/phpunit.yml?label=Tests&logo=github)](https://github.com/ChernegaSergiy/table-magic/actions/workflows/phpunit.yml)
[![Static Analysis](https://img.shields.io/github/actions/workflow/status/ChernegaSergiy/table-magic/phpstan.yml?label=PHPStan&logo=github)](https://github.com/ChernegaSergiy/table-magic/actions/workflows/phpstan.yml)

**TableMagic** is a powerful and flexible PHP library designed for creating and manipulating tables in console output. Inspired by Python's PrettyTable, TableMagic empowers developers to effortlessly display tabular data with customizable alignment, sorting, and styling options.

## Features

- **Easy Table Creation**: Quickly create tables with headers and rows.
- **Dynamic Row Addition**: Add rows easily with support for automatic column width adjustment.
- **Customizable Column Alignments**: Align columns to the left, right, or center.
- **UTF-8 Support**: Properly display non-ASCII characters.
- **Sorting Capability**: Sort tables by any column in ascending or descending order.
- **Export and Import Options**: Import data from CSV, JSON, and XML formats, and export to HTML, CSV, JSON, and XML.
- **Terminal Interaction**: Paginate through large tables in the console.

## Repository Structure

Here is the structure of the repository:

![Repository Structure](https://github.com/user-attachments/assets/827f0ab3-7dd3-4c86-862b-c68330da94f9)

## Installation

You can install TableMagic using Composer:

```bash
composer require chernegasergiy/table-magic
```

## Usage

Here's a quick example to get you started with TableMagic:

```php
require_once 'vendor/autoload.php';

use ChernegaSergiy\TableMagic\Table;

// Define headers and alignments
$headers = ['Employee ID', 'Name', 'Department', 'Performance Score', 'Review Date'];
$alignments = [
    'Employee ID' => 'r',
    'Performance Score' => 'r',
    'Review Date' => 'c',
];

// Create a new Table instance
$table = new Table($headers, $alignments);

// Add rows to the table
$table->addRow([1001, 'Alice Thompson', 'Marketing', 85, '2024-06-15']);
$table->addRow([1002, 'Brian Lee', 'Sales', 90, '2024-06-18']);
$table->addRow([1003, 'Carol Martinez', 'Engineering', 88, '2024-06-20']);

// Display the table
echo $table;

// Sort the table by 'Performance Score' (descending order)
$table->sortTable('Performance Score', 'desc');
echo "\n\nSorted by Performance Score (Descending):\n";
echo $table;
```

This will output:

```
+-------------+----------------+-------------+-------------------+-------------+
| Employee ID |      Name      | Department  | Performance Score | Review Date |
+-------------+----------------+-------------+-------------------+-------------+
|        1001 | Alice Thompson | Marketing   |                85 | 2024-06-15  |
|        1002 | Brian Lee      | Sales       |                90 | 2024-06-18  |
|        1003 | Carol Martinez | Engineering |                88 | 2024-06-20  |
+-------------+----------------+-------------+-------------------+-------------+

Sorted by Performance Score (Descending):
+-------------+----------------+-------------+-------------------+-------------+
| Employee ID |      Name      | Department  | Performance Score | Review Date |
+-------------+----------------+-------------+-------------------+-------------+
|        1002 | Brian Lee      | Sales       |                90 | 2024-06-18  |
|        1003 | Carol Martinez | Engineering |                88 | 2024-06-20  |
|        1001 | Alice Thompson | Marketing   |                85 | 2024-06-15  |
+-------------+----------------+-------------+-------------------+-------------+
```

### Importing Data

You can import data into the table from various formats:

```php
$tableImporter = new TableImporter();
$table = $tableImporter->import($data, 'json'); // 'csv', 'xml' are also supported
```

### Exporting Data

Export your table to different formats:

```php
$tableExporter = new TableExporter($table);
$htmlOutput = $tableExporter->export('html'); // 'csv', 'json', 'xml' are also supported
```

### Terminal Interaction

For large datasets, you can interactively paginate through the table:

```php
$terminalInteraction = new TerminalInteraction($table);
$terminalInteraction->run();
```

## Contributing

Contributions are welcome and appreciated! Here's how you can contribute:

1. Fork the project
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

Please make sure to update tests as appropriate and adhere to the existing coding style.

## License

This project is licensed under the CSSM Unlimited License v2 (CSSM-ULv2). See the [LICENSE](LICENSE) file for details.

## Acknowledgments

- Inspired by the Python [PrettyTable](https://github.com/jazzband/prettytable) library
- Thanks to all contributors who have helped shape TableMagic
