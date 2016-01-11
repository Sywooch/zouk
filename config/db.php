<?php

$dbConfig = [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=localhost;dbname=yii2basic',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8',
];

if (file_exists(__DIR__ . '/env/local/db.php')) {
    $dbConfig = array_merge(
        $dbConfig,
        require(__DIR__ . '/env/local/db.php')
    );
}

return $dbConfig;