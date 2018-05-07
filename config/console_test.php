<?php

/**
 * @var $db array
 * @var $components array
 * @var $params array
 */
require __DIR__ . '/init.php';

$db = yii\helpers\ArrayHelper::merge(
    $db,
    require __DIR__ . '/test_db.php'
);

$components = require __DIR__ . '/components.php';
$components['token']['availableTokenIds'] = [2];

$config = [
    'id' => 'basic-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'app\commands',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => $db + $components,
    'params' => $params,
    /*
    'controllerMap' => [
        'fixture' => [ // Fixture generation command line.
            'class' => 'yii\faker\FixtureController',
        ],
    ],
    */
];

return $config;
