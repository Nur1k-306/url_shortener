<?php
require_once __DIR__ . '/db.php';
$config = require __DIR__ . '/config.php';

function generate_code(int $length): string
{
    $alphabet = 'abcdefghiklmnoprstuvwxyz0123456789';
    $max      = strlen($alphabet) - 1;
    $code     = '';
    for ($i = 0; $i < $length; $i++) {
        $code .= $alphabet[random_int(0, $max)];
    }
    return $code;
}

function is_valid_url(string $url): bool
{
    return (bool)filter_var($url, FILTER_VALIDATE_URL);
}

function find_existing_code(string $url): ?string
{
    global $pdo;
    $sql = 'SELECT code FROM links 
              WHERE url = :url 
                AND (expire_at IS NULL OR expire_at > :now)
              LIMIT 1';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':url' => $url,
        ':now' => time(),
    ]);
    $row = $stmt->fetch();
    return $row ? $row['code'] : null;
}

function store_url(string $url, ?int $ttl): string
{
    global $pdo, $config;

    $existing = find_existing_code($url);
    if ($existing) {
        return $existing;
    }

    $sql  = 'INSERT INTO links (code, url, created_at, expire_at)
             VALUES (:code, :url, :created_at, :expire_at)';
    $stmt = $pdo->prepare($sql);

    do {
        $code   = generate_code($config['code_length']);
        $expire = $ttl ? time() + $ttl : null;

        try {
            $stmt->execute([
                ':code'       => $code,
                ':url'        => $url,
                ':created_at' => time(),
                ':expire_at'  => $expire,
            ]);
            break;
        } catch (PDOException $e) {
            $err = $e->getCode();
            if (!in_array($err, ['23000','23505'], true)) {
                throw $e;
            }
        }
    } while (true);

    return $code;
}

function fetch_link(string $code): ?array
{
    global $pdo;
    $stmt = $pdo->prepare('SELECT * FROM links WHERE code = :code');
    $stmt->execute([':code' => $code]);
    return $stmt->fetch() ?: null;
}

function increment_clicks(string $code): void
{
    global $pdo;
    $pdo->prepare('UPDATE links SET clicks = clicks + 1 WHERE code = :code')
        ->execute([':code' => $code]);
}

function delete_link(string $code): void
{
    global $pdo;
    $pdo->prepare('DELETE FROM links WHERE code = :code')
        ->execute([':code' => $code]);
}

function get_all_links(): array
{
    global $pdo;
    $stmt = $pdo->query(
        'SELECT code, url, created_at, expire_at, clicks
         FROM links
         ORDER BY created_at DESC'
    );
    return $stmt->fetchAll();
}