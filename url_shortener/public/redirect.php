<?php
require_once __DIR__ . '/../app/functions.php';
$config = require __DIR__ . '/../app/config.php';

$code = $_GET['code'] ?? '';

if (!preg_match('/^[a-z0-9]{1,' . $config['max_code_length'] . '}$/', $code)) {
    http_response_code(404);
    exit('Неверный код');
}

$link = fetch_link($code);
if (!$link) {
    http_response_code(404);
    exit('Код не найден');
}

if ($link['expire_at'] !== null && $link['expire_at'] < time()) {
    delete_link($code);
    http_response_code(410);
    exit('Срок действия истёк');
}

increment_clicks($code);
header('Location: ' . $link['url'], true, 302);
exit;