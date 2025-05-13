<?php
namespace App\Utils;

require_once 'SQLiteContext.php';

class Logger {
    private $db;
    private static $instance = null;

    private function __construct() {
        try {
            $dbPath = __DIR__ . '/../logs.db';
            $this->db = new \SQLite3($dbPath);
            $this->db->exec('PRAGMA foreign_keys = ON;');
            $this->db->exec('PRAGMA journal_mode = WAL;');
            
            $this->initializeDatabase();
            
            echo "<div class='success-message' style='display: none;'>Таблиці логів успішно створені</div>";
        } catch (\Exception $e) {
            
            echo "<div class='error-message' style='display: none;'>Помилка при створенні бази даних логів: " . $e->getMessage() . "</div>";
        }
    }

    private function initializeDatabase() {
        try {
            $this->db->exec('BEGIN TRANSACTION');
            
            $this->createTables();
            
            $this->seedTablesIfEmpty();
            
            $this->db->exec('COMMIT');
            return true;
        } catch (\Exception $e) {
            $this->db->exec('ROLLBACK');
            throw new \Exception("Помилка при ініціалізації бази даних: " . $e->getMessage());
        }
    }
    
    private function createTables() {
        // Загружаем SQL из файла
        $sql = file_get_contents(__DIR__ . '/../logs.sql');
        
        // Выполняем SQL-запросы
        $result = $this->db->exec($sql);
        
        if ($result === false) {
            throw new \Exception("Помилка при створенні таблиць логів");
        }
    }
    
    private function seedTablesIfEmpty() {
        // Проверяем, есть ли записи в таблице request_logs
        $check = $this->db->query("SELECT COUNT(*) as count FROM request_logs");
        $row = $check->fetchArray(SQLITE3_ASSOC);
        
        // Если таблица пуста, добавляем тестовые данные
        if ($row['count'] == 0) {
            // Тестовые запросы
            $this->db->exec("INSERT INTO request_logs (method, url, ip_address, user_agent, response_time, status_code) 
                            VALUES ('GET', '/index.php', '127.0.0.1', 'Mozilla/5.0', 120, 200)");
            
            // Тестовые ошибки
            $this->db->exec("INSERT INTO error_logs (error_level, error_message, error_file, error_line) 
                            VALUES ('NOTICE', 'Тестовое сообщение об ошибке', 'index.php', 10)");
            
            // Тестовые активности пользователей
            $this->db->exec("INSERT INTO user_activity_logs (user_id, action_type, action_details, ip_address) 
                            VALUES (1, 'LOGIN', 'Успешный вход в систему', '127.0.0.1')");
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Инициализирует таблицы логов с использованием транзакций
     * @return bool Результат операции
     */
    public function initLogTables() {
        try {
            return $this->initializeDatabase();
        } catch (\Exception $e) {
            echo "<div class='error-message'>Помилка при створенні таблиць логів: " . $e->getMessage() . "</div>";
            return false;
        }
    }

    public function logRequest($method, $url, $ip, $userAgent, $responseTime, $statusCode, $requestBody = null, $responseBody = null) {
        try {
            // Начинаем транзакцию
            $this->db->exec('BEGIN TRANSACTION');
            
            $sql = "INSERT INTO request_logs (method, url, ip_address, user_agent, response_time, status_code, request_body, response_body)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(1, $method, SQLITE3_TEXT);
            $stmt->bindValue(2, $url, SQLITE3_TEXT);
            $stmt->bindValue(3, $ip, SQLITE3_TEXT);
            $stmt->bindValue(4, $userAgent, SQLITE3_TEXT);
            $stmt->bindValue(5, $responseTime, SQLITE3_INTEGER);
            $stmt->bindValue(6, $statusCode, SQLITE3_INTEGER);
            $stmt->bindValue(7, $requestBody, SQLITE3_TEXT);
            $stmt->bindValue(8, $responseBody, SQLITE3_TEXT);
            
            $result = $stmt->execute();
            
            if ($result === false) {
                throw new \Exception("Помилка при логуванні запроса");
            }
            
            $requestId = $this->db->lastInsertRowID();
            
            $this->db->exec('COMMIT');
            
            return $requestId;
        } catch (\Exception $e) {
            $this->db->exec('ROLLBACK');
            error_log("Failed to log request: " . $e->getMessage());
            return false;
        }
    }

    public function logError($level, $message, $file = null, $line = null, $trace = null, $requestId = null, $userId = null) {
        try {
            // Начинаем транзакцию
            $this->db->exec('BEGIN TRANSACTION');
            
            $sql = "INSERT INTO error_logs (error_level, error_message, error_file, error_line, error_trace, request_id, user_id)
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(1, $level, SQLITE3_TEXT);
            $stmt->bindValue(2, $message, SQLITE3_TEXT);
            $stmt->bindValue(3, $file, SQLITE3_TEXT);
            $stmt->bindValue(4, $line, SQLITE3_INTEGER);
            $stmt->bindValue(5, $trace, SQLITE3_TEXT);
            $stmt->bindValue(6, $requestId, SQLITE3_INTEGER);
            $stmt->bindValue(7, $userId, SQLITE3_INTEGER);
            
            $result = $stmt->execute();
            
            if ($result === false) {
                throw new \Exception("Ошибка при логировании ошибки");
            }
            
            // Фиксируем транзакцию
            $this->db->exec('COMMIT');
            
            return $this->db->lastInsertRowID();
        } catch (\Exception $e) {
            // Откатываем транзакцию в случае ошибки
            $this->db->exec('ROLLBACK');
            error_log("Failed to log error: " . $e->getMessage());
            return false;
        }
    }

    public function logUserActivity($userId, $actionType, $actionDetails = null, $ip = null, $requestId = null) {
        try {
            
            $this->db->exec('BEGIN TRANSACTION');
            
            $sql = "INSERT INTO user_activity_logs (user_id, action_type, action_details, ip_address, request_id)
                    VALUES (?, ?, ?, ?, ?)";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(1, $userId, SQLITE3_INTEGER);
            $stmt->bindValue(2, $actionType, SQLITE3_TEXT);
            $stmt->bindValue(3, $actionDetails, SQLITE3_TEXT);
            $stmt->bindValue(4, $ip, SQLITE3_TEXT);
            $stmt->bindValue(5, $requestId, SQLITE3_INTEGER);
            
            $result = $stmt->execute();
            
            if ($result === false) {
                throw new \Exception("Ошибка при логировании активности пользователя");
            }
            
            
            $this->db->exec('COMMIT');
            
            return $this->db->lastInsertRowID();
        } catch (\Exception $e) {
           
            $this->db->exec('ROLLBACK');
            error_log("Failed to log user activity: " . $e->getMessage());
            return false;
        }
    }

    public function getRecentLogs($limit = 100) {
        try {
            $sql = "SELECT * FROM (
                SELECT 'request' as type, timestamp, method, url, status_code as code
                FROM request_logs
                UNION ALL
                SELECT 'error' as type, timestamp, error_level as method, error_message as url, NULL as code
                FROM error_logs
                UNION ALL
                SELECT 'activity' as type, timestamp, action_type as method, action_details as url, NULL as code
                FROM user_activity_logs
            ) ORDER BY timestamp DESC LIMIT " . (int)$limit;
            
            $result = $this->db->query($sql);
            
            $rows = [];
            if ($result) {
                while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
                    $rows[] = $row;
                }
            }
            
            return $rows;
        } catch (\Exception $e) {
            error_log("Failed to get recent logs: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Извлекает IP-адрес из заголовков запроса с помощью регулярных выражений
     * @return string IP-адрес
     */
    public static function getClientIp() {
        $ipAddress = '';
        
        // Проверяем различные заголовки
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ipAddress = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            // HTTP_X_FORWARDED_FOR может содержать список IP-адресов
            $ipList = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $ipAddress = trim($ipList[0]);
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED'])) {
            $ipAddress = $_SERVER['HTTP_X_FORWARDED'];
        } elseif (!empty($_SERVER['HTTP_FORWARDED_FOR'])) {
            $ipAddress = $_SERVER['HTTP_FORWARDED_FOR'];
        } elseif (!empty($_SERVER['HTTP_FORWARDED'])) {
            $ipAddress = $_SERVER['HTTP_FORWARDED'];
        } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
            $ipAddress = $_SERVER['REMOTE_ADDR'];
        }
        
        // Проверка вместо использования isValidIpAddress
        if (filter_var($ipAddress, FILTER_VALIDATE_IP) === false) {
            $ipAddress = 'UNKNOWN';
        }
        
        return $ipAddress;
    }
    
    /**
     * Анализирует строку лога и извлекает из нее данные с помощью регулярных выражений
     * @param string $logString Строка лога
     * @return array Структурированные данные
     */
    public static function parseLogString($logString) {
        $result = [
            'timestamp' => null,
            'level' => null,
            'message' => null,
            'context' => []
        ];
        
        // Извлекаем временную метку и уровень сообщения
        $pattern = '/^\[([^\]]+)\]\s+(\w+):\s+(.*)$/';
        if (preg_match($pattern, $logString, $matches)) {
            $result['timestamp'] = $matches[1];
            $result['level'] = $matches[2];
            $result['message'] = $matches[3];
            
            // Ищем дополнительный контекст в фигурных скобках
            $contextPattern = '/\{([^}]+)\}/';
            if (preg_match_all($contextPattern, $matches[3], $contextMatches)) {
                foreach ($contextMatches[1] as $contextMatch) {
                    // Разбиваем по парам ключ:значение
                    $pairs = explode(',', $contextMatch);
                    foreach ($pairs as $pair) {
                        $keyValue = explode(':', $pair, 2);
                        if (count($keyValue) === 2) {
                            $key = trim($keyValue[0]);
                            $value = trim($keyValue[1]);
                            $result['context'][$key] = $value;
                        }
                    }
                }
                
                // Удаляем контекст из сообщения
                $result['message'] = preg_replace($contextPattern, '', $result['message']);
            }
        }
        
        return $result;
    }
    
    /**
     * Форматирует сообщение журнала в соответствии с указанным форматом
     * @param string $message Исходное сообщение
     * @param string $format Формат (TEXT, JSON, HTML)
     * @return string Отформатированное сообщение
     */
    public static function formatLogMessage($message, $format = 'TEXT') {
        switch (strtoupper($format)) {
            case 'JSON':
                $data = self::parseLogString($message);
                return json_encode($data, JSON_UNESCAPED_UNICODE);
                
            case 'HTML':
                $data = self::parseLogString($message);
                $html = '<div class="log-entry">';
                $html .= '<span class="log-timestamp">' . htmlspecialchars($data['timestamp']) . '</span> ';
                $html .= '<span class="log-level log-level-' . strtolower($data['level']) . '">' . htmlspecialchars($data['level']) . '</span>: ';
                $html .= '<span class="log-message">' . htmlspecialchars($data['message']) . '</span>';
                
                if (!empty($data['context'])) {
                    $html .= '<div class="log-context">';
                    foreach ($data['context'] as $key => $value) {
                        $html .= '<div class="log-context-item">';
                        $html .= '<span class="log-context-key">' . htmlspecialchars($key) . ':</span> ';
                        $html .= '<span class="log-context-value">' . htmlspecialchars($value) . '</span>';
                        $html .= '</div>';
                    }
                    $html .= '</div>';
                }
                
                $html .= '</div>';
                return $html;
                
            case 'TEXT':
            default:
                return $message;
        }
    }
} 