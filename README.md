# TableMagic

[![Latest Stable Version](https://img.shields.io/packagist/v/chernegasergiy/table-magic.svg?label=Packagist&logo=packagist)](https://packagist.org/packages/chernegasergiy/table-magic) [![Total Downloads](https://img.shields.io/packagist/dt/chernegasergiy/table-magic.svg?label=Downloads&logo=packagist)](https://packagist.org/packages/chernegasergiy/table-magic) [![License](https://img.shields.io/packagist/l/chernegasergiy/table-magic.svg?label=Licence&logo=open-source-initiative)](https://packagist.org/packages/chernegasergiy/table-magic) [![Tests](https://img.shields.io/github/actions/workflow/status/ChernegaSergiy/table-magic/phpunit.yml?label=Tests&logo=github)](https://github.com/ChernegaSergiy/table-magic/actions/workflows/phpunit.yml) [![Test Coverage](https://img.shields.io/codecov/c/github/ChernegaSergiy/table-magic?label=Test%20Coverage&logo=codecov)](https://app.codecov.io/gh/ChernegaSergiy/table-magic) [![Static Analysis](https://img.shields.io/github/actions/workflow/status/ChernegaSergiy/table-magic/phpstan.yml?label=PHPStan&logo=github)](https://github.com/ChernegaSergiy/table-magic/actions/workflows/phpstan.yml)

**TableMagic** is a powerful and flexible PHP library designed for creating and manipulating tables in console output. Inspired by Python's PrettyTable, TableMagic empowers developers to effortlessly display tabular data with customizable alignment, sorting, and styling options.

## Features

- **Easy Table Creation**: Quickly create tables with headers and rows.
- **Dynamic Row Addition**: Add rows easily with support for automatic column width adjustment.
- **Customizable Column Alignments**: Align columns to the left, right, or center.
- **UTF-8 Support**: Properly display non-ASCII characters.
- **Sorting Capability**: Sort tables by any column in ascending or descending order.
- **Export and Import Options**: Import data from CSV, JSON, and XML formats, and export to HTML, CSV, JSON, and XML using a flexible, object-oriented approach with dedicated importer and exporter classes.
- **Customizable Table Styles**: Apply various predefined styles or define your own for unique table appearances.
- **Terminal Interaction**: Paginate through large tables in the console.

## Repository Structure

Here is the updated structure of the core `src/TableMagic` directory:

```
src/
\-- TableMagic/
    +-- Table.php
    +-- TableExporter.php
    +-- TableImporter.php
    +-- TableStyle.php
    +-- TableStyleRegistry.php
    +-- TerminalInteraction.php
    +-- Interfaces/                # Defines contracts for importers and exporters
    |   +-- TableImporterInterface.php
    |   \-- TableExporterInterface.php
    +-- Importers/                 # Concrete implementations for importing various formats
    |   +-- CsvTableImporter.php
    |   +-- JsonTableImporter.php
    |   \-- XmlTableImporter.php
    \-- Exporters/                 # Concrete implementations for exporting to various formats
        +-- CsvTableExporter.php
        +-- HtmlTableExporter.php
        +-- JsonTableExporter.php
        \-- XmlTableExporter.php
```

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

## Table Styling

TableMagic offers extensive customization options for table appearance through its styling system. You can apply various predefined styles or define your own custom styles to match your application's aesthetic.

### Predefined Styles

TableMagic comes with several built-in styles, including: `default`, `compact`, `dots`, `rounded`, `unicode-single-line`, `unicode-double-line`, `github-markdown`, `reddit-markdown`, `restructured-text-grid`, and `restructured-text-simple`.

Here's an example demonstrating some of these styles, along with a custom one:

```php
require_once 'vendor/autoload.php';

use ChernegaSergiy\TableMagic\Table;
use ChernegaSergiy\TableMagic\TableStyle;
use ChernegaSergiy\TableMagic\TableStyleRegistry;

$table = new Table(
    ['Product', 'Quantity', 'Price'],
    [
        'Quantity' => 'c', // Center align Quantity
        'Price' => 'r'     // Right align Price
    ]
);
$table->addRow(['Laptop', 2, 1200.50]);
$table->addRow(['Mouse', 5, 25.00]);
$table->addRow(['Keyboard', 1, 75.99]);

echo "Default Style:\n";
$table->setStyle('default');
echo $table;

echo "\nCompact Style:\n";
$table->setStyle('compact');
echo $table;

echo "\nUnicode Single Line Style:\n";
$table->setStyle('unicode-single-line');
echo $table;

// Define and apply a custom style
$custom_style = new TableStyle(
    '¦', // Vertical separator (light vertical bar)
    ['╭', '─', '┬', '╮'], // Top border (rounded corners)
    ['├', '─', '┼', '┤'], // Header separator (light lines)
    ['¦', '┄', '┼', '¦'], // Row separator (dashed horizontal, light vertical)
    ['╰', '─', '┴', '╯']  // Bottom border (rounded corners)
);
TableStyleRegistry::register('custom-fancy', $custom_style);

echo "\nCustom 'Fancy' Style:\n";
$table->setStyle('custom-fancy');
echo $table;
```

This will output:

```
Default Style:
+----------+----------+--------+
| Product  | Quantity |  Price |
+----------+----------+--------+
| Laptop   |    2     | 1200.5 |
| Mouse    |    5     |     25 |
| Keyboard |    1     |  75.99 |
+----------+----------+--------+

Compact Style:
  Product    Quantity    Price 
 ---------- ---------- -------- 
  Laptop        2       1200.5 
  Mouse         5           25 
  Keyboard      1        75.99 

Unicode Single Line Style:
┌──────────┬──────────┬────────┐
│ Product  │ Quantity │  Price │
├──────────┼──────────┼────────┤
│ Laptop   │    2     │ 1200.5 │
│ Mouse    │    5     │     25 │
│ Keyboard │    1     │  75.99 │
└──────────┴──────────┴────────┘

Custom 'Fancy' Style:
╭──────────┬──────────┬────────╮
¦ Product  ¦ Quantity ¦  Price ¦
├──────────┼──────────┼────────┤
¦ Laptop   ¦    2     │ 1200.5 ¦
¦ Mouse    ¦    5     │     25 ¦
¦ Keyboard │    1     │  75.99 ¦
╰──────────┴──────────┴────────╯
```

### Importing Data

Data can be imported using the `TableImporter` factory, which delegates to specific importer classes based on the format:

```php
use ChernegaSergiy\TableMagic\TableImporter;

$importer = new TableImporter();
$csv_data = "Name,Age\nAlice,30\nBob,25";
$table_from_csv = $importer->import($csv_data, 'csv');

$json_data = '{"headers":["Name","Age"],"rows":[["Charlie",30],["David",25]]}';
$table_from_json = $importer->import($json_data, 'json');

$xml_data = '<root><headers><header>Name</header><header>Age</header></headers><rows><row><Name>Eve</Name><Age>30</Age></row></rows></root>';
$table_from_xml = $importer->import($xml_data, 'xml');

// You can also directly use specific importer classes:
use ChernegaSergiy\TableMagic\Importers\CsvTableImporter;
$csvImporter = new CsvTableImporter();
$table_direct_csv = $csvImporter->import($csv_data);
```

### Exporting Data

Tables can be exported using the `TableExporter` factory, which delegates to specific exporter classes based on the format:

```php
use ChernegaSergiy\TableMagic\TableExporter;

$table_to_export = new Table(['Col1'], ['Val1']);
$exporter = new TableExporter($table_to_export);

$html_output = $exporter->export('html');
$csv_output = $exporter->export('csv');
$json_output = $exporter->export('json');
$xml_output = $exporter->export('xml');

// You can also directly use specific exporter classes:
use ChernegaSergiy\TableMagic\Exporters\HtmlTableExporter;
$htmlExporter = new HtmlTableExporter();
$direct_html_output = $htmlExporter->export($table_to_export);
```

### Terminal Interaction

For large datasets, you can interactively paginate through the table:

```php
use ChernegaSergiy\TableMagic\TerminalInteraction;

$terminal_interaction = new TerminalInteraction($table);
$terminal_interaction->run();
```

An example script demonstrating terminal interaction is available at `examples/run_terminal_interaction.php`.

## Contributing

Contributions are welcome and appreciated! Here's how you can contribute:

1. Fork the project
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

Please make sure to update tests as appropriate and adhere to the existing coding style.

## License

This project is licensed under the CSSM Unlimited License v2.0 (CSSM-ULv2). See the [LICENSE](LICENSE) file for details.

## Acknowledgments

- Inspired by the Python [PrettyTable](https://github.com/jazzband/prettytable) library
- Thanks to all contributors who have helped shape TableMagic
