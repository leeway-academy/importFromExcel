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