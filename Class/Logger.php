<?php
require_once 'SQLiteContext.php';

class Logger {
    private $db;
    private static $instance = null;

    private function __construct() {
        $this->db = SQLiteContext::getInstance();
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function logRequest($method, $url, $ip, $userAgent, $responseTime, $statusCode, $requestBody = null, $responseBody = null) {
        try {
            $sql = "INSERT INTO request_logs (method, url, ip_address, user_agent, response_time, status_code, request_body, response_body)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            
            $result = $this->db->query($sql, [
                $method, $url, $ip, $userAgent, $responseTime, $statusCode, $requestBody, $responseBody
            ]);
            
            return $result !== false;
        } catch (Exception $e) {
            error_log("Failed to log request: " . $e->getMessage());
            return false;
        }
    }

    public function logError($level, $message, $file = null, $line = null, $trace = null, $requestId = null, $userId = null) {
        try {
            $sql = "INSERT INTO error_logs (error_level, error_message, error_file, error_line, error_trace, request_id, user_id)
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            
            $result = $this->db->query($sql, [
                $level, $message, $file, $line, $trace, $requestId, $userId
            ]);
            
            return $result !== false;
        } catch (Exception $e) {
            error_log("Failed to log error: " . $e->getMessage());
            return false;
        }
    }

    public function logUserActivity($userId, $actionType, $actionDetails = null, $ip = null, $requestId = null) {
        try {
            $sql = "INSERT INTO user_activity_logs (user_id, action_type, action_details, ip_address, request_id)
                    VALUES (?, ?, ?, ?, ?)";
            
            $result = $this->db->query($sql, [
                $userId, $actionType, $actionDetails, $ip, $requestId
            ]);
            
            return $result !== false;
        } catch (Exception $e) {
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
            ) ORDER BY timestamp DESC LIMIT ?";
            
            $result = $this->db->query($sql, [$limit]);
            return $this->db->fetchAll($result);
        } catch (Exception $e) {
            error_log("Failed to get recent logs: " . $e->getMessage());
            return [];
        }
    }
} 