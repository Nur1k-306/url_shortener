<?php
session_start();
require_once __DIR__ . '/../app/functions.php';
$config   = require __DIR__ . '/../app/config.php';
$allLinks = get_all_links();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>URL Shortener</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<h1>Сократить ссылку</h1>

<?php if (!empty($_SESSION['msg'])): ?>
    <div class="<?= $_SESSION['msg_type'] ?? 'msg' ?>">
        <?= $_SESSION['msg'] ?>
    </div>
    <?php unset($_SESSION['msg'], $_SESSION['msg_type']); ?>
<?php endif; ?>

<form action="shorten.php" method="post" novalidate>
    <label>
        Полный URL:
        <input type="url" name="long_url" placeholder="https://example.com/…" required>
    </label>

    <label>
        TTL (секунды, ≥60, опционально):
        <input type="number" name="ttl" min="60" placeholder="например, 3600">
    </label>

    <button type="submit">Сократить</button>
</form>

<?php if (count($allLinks) > 0): ?>
    <h2>Статистика по ссылкам</h2>
    <table>
        <thead>
        <tr>
            <th>Код</th>
            <th>Ссылка</th>
            <th>Создано</th>
            <th>Истекает</th>
            <th>Клики</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($allLinks as $ln): ?>
            <tr>
                <td>
                    <a href="/<?= htmlspecialchars($ln['code']) ?>">
                        <?= htmlspecialchars($ln['code']) ?>
                    </a>
                </td>
                <td class="small"><?= htmlspecialchars($ln['url']) ?></td>
                <td class="small"><?= date('Y-m-d H:i', $ln['created_at']) ?></td>
                <td class="small">
                    <?= $ln['expire_at'] ? date('Y-m-d H:i', $ln['expire_at']) : '∞' ?>
                </td>
                <td><?= (int)$ln['clicks'] ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
</body>
</html>