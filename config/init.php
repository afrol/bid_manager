<?php

$db = require __DIR__ . '/db.php';
if (file_exists(PROD_CONFIG_DIR . '/db.php')) {
    $db = yii\helpers\ArrayHelper::merge(
        $db,
        require PROD_CONFIG_DIR . '/db.php'
    );
}

$components = require __DIR__ . '/components.php';
if (file_exists(PROD_CONFIG_DIR . '/components.php')) {
    $components = yii\helpers\ArrayHelper::merge(
        $components,
        require PROD_CONFIG_DIR . '/components.php'
    );
}

$params = require __DIR__ . '/params.php';
if (file_exists(PROD_CONFIG_DIR . '/params.php')) {
    $params = yii\helpers\ArrayHelper::merge(
        $params,
        require PROD_CONFIG_DIR . '/params.php'
    );
}
