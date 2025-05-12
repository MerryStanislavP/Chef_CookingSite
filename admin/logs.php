<?php
session_start();
require_once __DIR__ . '/../autoload.php';
require_once __DIR__ . '/../Class/db.php';
require_once __DIR__ . '/../Class/users.php';
require_once __DIR__ . '/../Class/Logger.php';

use App\Utils\Logger;
use App\Models\User;

// Перевірка чи користувач авторизований
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

// Перевірка чи користувач є адміном
$user = new User();
if (!$user->isAdmin($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

$logger = Logger::getInstance();
$logs = $logger->getRecentLogs(100);
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Логи сайту</title>
    <link rel="stylesheet" href="../Style/style_head.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            margin-bottom: 20px;
        }
        .log-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .log-table th, .log-table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        .log-table th {
            background-color: #f2f2f2;
        }
        .log-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .log-type {
            font-weight: bold;
        }
        .type-request { color: #2196F3; }
        .type-error { color: #F44336; }
        .type-activity { color: #4CAF50; }
        .admin-nav {
            margin-bottom: 20px;
        }
        .admin-nav a {
            color: #ff6b6b;
            text-decoration: none;
            margin-right: 15px;
        }
        .admin-nav a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="admin-nav">
            <a href="../index.php">На головну</a>
            <a href="logs.php">Логи</a>
        </div>
        <h1>Логи сайту</h1>
        <table class="log-table">
            <thead>
                <tr>
                    <th>Тип</th>
                    <th>Дата/Час</th>
                    <th>Метод/Дія</th>
                    <th>URL/Повідомлення</th>
                    <th>Код</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($logs as $log): ?>
                    <tr>
                        <td class="log-type type-<?= $log['type'] ?>"><?= ucfirst($log['type']) ?></td>
                        <td><?= date('Y-m-d H:i:s', strtotime($log['timestamp'])) ?></td>
                        <td><?= htmlspecialchars($log['method']) ?></td>
                        <td><?= htmlspecialchars($log['url']) ?></td>
                        <td><?= $log['code'] ?? '-' ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html> 