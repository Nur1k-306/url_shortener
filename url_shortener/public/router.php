<?php

date_default_timezone_set('Europe/Moscow');
$uri      = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$filepath = __DIR__ . $uri;

if ($uri !== '/' && file_exists($filepath)) {
    return false;
}

if ($uri === '/' || $uri === '') {
    require __DIR__ . '/index.php';
} else {
    $_GET['code'] = ltrim($uri, '/');
    require __DIR__ . '/redirect.php';
}