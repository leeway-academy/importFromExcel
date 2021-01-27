<?php

require_once 'vendor/autoload.php';

if ($argc < 2) {
    die('Please indicate Excel file to import');
}

$config = require_once 'config.inc.php';

try {
    $reader = PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($argv[1]);
} catch ( \PhpOffice\PhpSpreadsheet\Reader\Exception $exception ) {

    die ('The file '.$argv[1].' does not seem to be a SpreadSheet');
}

$spreadhseet = $reader->load($argv[1]);
$sheet = $spreadhseet->getActiveSheet();

$titles = array_map(fn(array $columnDef): string => $columnDef['title'], $config['columns']);

$columnField = [];
$rowIterator = $sheet->getRowIterator();
foreach ($rowIterator->current()->getCellIterator() as $cell) {
    if (($field = array_search(strtolower($cell->getValue()), $titles)) !== false) {
        $columnField[$cell->getColumn()] = $field;
    }
}

$sql = "INSERT INTO {$config['target_table']} (";
foreach ($columnField as $field) {
    $sql .= "$field, ";
}

$sql = substr($sql, 0, -2) . ") VALUES (";

foreach ($columnField as $field ) {
    $sql .= ":{$field}, ";
}

$sql = substr($sql, 0, -2) . ");";

echo 'SQL = '.$sql.PHP_EOL;

try {
    $pdo = new PDO($config['dsn'], $config['user'], $config['pass'], [PDO::ERRMODE_EXCEPTION => true]);
} catch (PDOException $exception) {
    die('Can not connect to database: '.$exception->getMessage());
}

$st = $pdo->prepare($sql);

foreach ($rowIterator as $row) {
    if ($row->getRowIndex() == 1) {
        continue;
    }

    foreach ($row->getCellIterator() as $cell) {
        if (array_key_exists($cell->getColumn(), $columnField)) {
            $field = $columnField[$cell->getColumn()];
            $value = array_key_exists('transformation', $config['columns'][$field]) ? $config['columns'][$field]['transformation']($cell->getValue()) : $cell->getValue();
            $st->bindValue(':'.$field, $value);
        }
    }

    try {
        $st->execute();
        echo 'Row '.$row->getRowIndex().' imported'.PHP_EOL;
    } catch (PDOException $exception) {
        echo 'Failed to process row: '.$row->getRowIndex().': '.$exception->getMessage().PHP_EOL;
    }
}
