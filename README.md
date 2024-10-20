# TableMagic

[![Latest Stable Version](https://img.shields.io/packagist/v/chernegasergiy/table-magic.svg)](https://packagist.org/packages/chernegasergiy/table-magic)
[![Total Downloads](https://img.shields.io/packagist/dt/chernegasergiy/table-magic.svg)](https://packagist.org/packages/chernegasergiy/table-magic)
[![License](https://img.shields.io/packagist/l/chernegasergiy/table-magic.svg)](https://packagist.org/packages/chernegasergiy/table-magic)

**TableMagic** is a powerful and flexible PHP library for creating beautifully formatted tables in the console. Inspired by Python's PrettyTable, TableMagic empowers developers to effortlessly display tabular data with customizable alignment, sorting, and styling options.

## Features

- Easy table creation and row addition
- Dynamic column sorting
- Customizable column alignments (left, right, center)
- UTF-8 support for proper display of non-ASCII characters
- Automatic column width adjustment
- Formatted string output for easy integration

## Installation

Install TableMagic using Composer:

```bash
composer require chernegasergiy/table-magic
```

## Usage

Here's a quick example to get you started with TableMagic:

```php
require_once 'vendor/autoload.php';

use ChernegaSergiy\TableMagic\TableMagic;

// Define headers and alignments
$headers = ['Employee ID', 'Name', 'Department', 'Performance Score', 'Review Date'];
$alignments = [
    'Employee ID' => 'r',
    'Performance Score' => 'r',
    'Review Date' => 'c'
];

// Create a new TableMagic instance
$table = new TableMagic($headers, $alignments);

// Add rows to the table
$table->addRow([1001, 'Alice Thompson', 'Marketing', 85, '2024-06-15']);
$table->addRow([1002, 'Brian Lee', 'Sales', 90, '2024-06-18']);
$table->addRow([1003, 'Carol Martinez', 'Engineering', 88, '2024-06-20']);

// Display the table
echo $table;

// Sort the table by 'Performance Score' (descending order)
$table->sortTable('Performance Score', SORT_DESC);
echo "\nSorted by Performance Score (Descending):\n";
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

## Contributing

Contributions are welcome and appreciated! Here's how you can contribute:

1. Fork the project
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

Please make sure to update tests as appropriate and adhere to the existing coding style.

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Acknowledgements

- Inspired by the Python [PrettyTable](https://github.com/jazzband/prettytable) library
- Thanks to all contributors who have helped shape TableMagic
