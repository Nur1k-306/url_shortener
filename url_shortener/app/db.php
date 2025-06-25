<?php
$config = require __DIR__ . '/config.php';

try {
    $pdo = new PDO(
        $config['db_dsn'],
        $config['db_user'],
        $config['db_password'],
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
}catch (PDOException $e){
    exit('Ошибка подключения к бд:'.$e->getMessage());
}

$pdo->exec(file_get_contents(__DIR__ . '/schema.sql'));