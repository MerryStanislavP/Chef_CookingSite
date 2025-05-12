<?php
require_once 'autoload.php';
require_once 'Class/Logger.php';

use App\Utils\Logger;

// Заголовок страницы
echo '<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Встановлення системи логування</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            text-align: center;
        }
        .success-message {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            padding: 10px 15px;
            margin: 15px 0;
            border-radius: 4px;
        }
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            padding: 10px 15px;
            margin: 15px 0;
            border-radius: 4px;
        }
        .step {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        .step-title {
            font-weight: bold;
            margin-bottom: 10px;
        }
        .btn {
            display: inline-block;
            padding: 10px 15px;
            background-color: #ff6b6b;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 20px;
        }
        .btn:hover {
            background-color: #e05555;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Встановлення системи логування</h1>';

// Проверяем, если был отправлен POST запрос на инициализацию
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['init_logs'])) {
    // Получаем экземпляр логгера
    $logger = Logger::getInstance();
    
    // Инициализируем таблицы логов с использованием транзакций
    $result = $logger->initLogTables();
    
    if ($result) {
        // Записываем первый лог для проверки
        $requestId = $logger->logRequest(
            $_SERVER['REQUEST_METHOD'],
            $_SERVER['REQUEST_URI'],
            Logger::getClientIp(),
            $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown',
            0,
            200,
            null,
            null
        );
        
        if ($requestId) {
            echo '<div class="success-message">Система логування успішно ініціалізована та протестована!</div>';
            echo '<div class="step">
                    <div class="step-title">Деталі:</div>
                    <p>- Створені всі необхідні таблиці</p>
                    <p>- Записаний тестовий лог із ID: ' . $requestId . '</p>
                    <p>- IP-адреса: ' . htmlspecialchars(Logger::getClientIp()) . '</p>
                  </div>';
            
            // Демонстрация обработки строки логов с использованием регулярных выражений
            $logString = '[' . date('Y-m-d H:i:s') . '] INFO: Installation completed successfully {user:admin, ip:' . Logger::getClientIp() . '}';
            $parsedLog = Logger::parseLogString($logString);
            
            echo '<div class="step">
                    <div class="step-title">Приклад парсингу логу з використанням регулярних виразів:</div>
                    <p>Вихідний рядок: <code>' . htmlspecialchars($logString) . '</code></p>
                    <p>Результат парсингу:</p>
                    <pre>' . htmlspecialchars(print_r($parsedLog, true)) . '</pre>
                    <p>Відформатований як HTML:</p>
                    ' . Logger::formatLogMessage($logString, 'HTML') . '
                  </div>';
        }
    }
}

// Форма для инициализации
echo '<div class="step">
        <div class="step-title">Ініціалізація таблиць логів</div>
        <p>Натисніть кнопку нижче для створення таблиць логування з використанням транзакцій:</p>
        <p>- request_logs - логи HTTP-запитів</p>
        <p>- error_logs - логи помилок</p>
        <p>- user_activity_logs - логи активності користувачів</p>
        <form method="POST">
            <button type="submit" name="init_logs" value="1" class="btn">Ініціалізувати таблиці логів</button>
        </form>
      </div>
      
      <div class="step">
        <div class="step-title">Повернутися до сайту</div>
        <a href="index.php" class="btn">На головну</a>
      </div>
    </div>
</body>
</html>'; 