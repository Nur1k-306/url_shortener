<?php
session_start();
require_once __DIR__ . '/../app/functions.php';
$config = require __DIR__ . '/../app/config.php';

$url = trim($_POST['long_url'] ?? '');
if (!is_valid_url($url)) {
    $_SESSION['msg'] = '❌ Введите корректный URL.';
    $_SESSION['msg_type'] = 'error';
    header('Location: index.php');
    exit;
}

$ttl_raw = trim($_POST['ttl'] ?? '');
if ($ttl_raw !== '') {
    if (!ctype_digit($ttl_raw) || (int)$ttl_raw < 60) {
        $_SESSION['msg'] = '❌ TTL должен быть целым числом ≥ 60.';
        $_SESSION['msg_type'] = 'error';
        header('Location: index.php');
        exit;
    }
    $ttl = (int)$ttl_raw;
} else {
    $ttl = null;
}

$code = store_url($url, $ttl);

$linkData = fetch_link($code);
$clicks   = $linkData ? (int)$linkData['clicks'] : 0;

$short   = '/' . $code;
$message = sprintf(
    '✅ Ссылка: <a href="%1$s">%1$s</a> — кликов: %2$d',
    htmlspecialchars($short),
    $clicks
);

$_SESSION['msg'] = $message;
$_SESSION['msg_type'] = 'msg';
header('Location: index.php');
exit;