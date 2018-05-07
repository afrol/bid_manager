<?php

return [
    'db' => [
        'class' => 'yii\db\Connection',
        'dsn' => '',
        'username' => '',
        'password' => '',
        'charset' => 'utf8',

        // Schema cache options (for production environment)
        'enableSchemaCache' => true,
        'schemaCacheDuration' => 60,
        'schemaCache' => 'cache',
    ],
    'mssql_dwh' => [
        'class' => 'yii\db\Connection',
        'charset' => 'utf8',
        'username' => '',
        'password' => '',
        'dsn' => '',
    ],
];
