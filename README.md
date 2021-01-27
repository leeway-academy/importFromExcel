# importFromExcel

This script is a small Proof of concept for how to create a generic Excel importing mechanism with PHP.

## Configuration

The whole configuration is stored in the file `config.inc.php`, as in the sample:

```php
<?php

return [
    'columns' => [
        'name' => [
            'title' => 'nombre',
            'transformation' => fn(string $name): string => ucfirst($name),
        ],
        'email' => [
            'title' => 'email',
            'transformation' => fn(string $email): string => filter_var($email, FILTER_VALIDATE_EMAIL),
        ],
    ],
    'target_table' => 'clients',
    'dsn' => 'sqlite:/home/mauro/Code/importFromExcel/mydb.sq3',
    'user' => null,
    'pass' => null,
];
```

The configuration file consists of an array specifying:

* A target table (the table which will be used to insert the newly created records)
* A dsn containing the details of the DB connection as needed by [PDO](https://www.php.net/manual/es/pdo.construct.php)
* User and Password for connecting to the database
* An array of column definitions

The main part is, of course, the column definition array.

The structure of this array is as follows:

* The key is the field name within the target table
* The value is an array consisting of two fields:
  1. A title which corresponds to the column title within the SpreadSheet
  2. (Optional) A transformation: a function to be applied to the value in the spreadsheet before inserting to the database
  
# Installing it

1. Install [composer](https://getcomposer.org/)
2. Run the command `php composer.php install`

# Running it

In order to run this script you need:

1. A spreadsheet that can be read using [PhpSpreadSheet](https://phpspreadsheet.readthedocs.io/en/latest/) with a first row defining column titles and a set of rows with the same structure.
2. A database that is supported by PDO and that you can access
3. A php interpreter (To run this particular example you'll need php >=7.4, but the config file can be adapted to use a prior version)

Then you simply need to run the command `php import.php SPREADSHEET_FILE_NAME`

If you have any suggestion or question please contact me at mauro.chojrin@leewayweb.com
