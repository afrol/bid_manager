<?php

/**
 * @var $db array
 * @var $components array
 * @var $params array
 */
require __DIR__ . '/init.php';

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
];

return $config;
