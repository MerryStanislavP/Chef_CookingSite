<?php
class SQLiteContext {
    private static $instance = null;
    private $connection;
    private $dbPath;

    private function __construct() {
        $this->dbPath = __DIR__ . '/../logs.db';
        $this->connect();
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function connect() {
        try {
            $this->connection = new SQLite3($this->dbPath);
            $this->connection->exec('PRAGMA foreign_keys = ON;');
            $this->connection->exec('PRAGMA journal_mode = WAL;');
            $this->initializeDatabase();
        } catch (Exception $e) {
            die("SQLite connection failed: " . $e->getMessage());
        }
    }

    private function initializeDatabase() {
        $sql = file_get_contents(__DIR__ . '/../logs.sql');
        $this->connection->exec($sql);
    }

    public function getConnection() {
        return $this->connection;
    }

    public function query($sql, $params = []) {
        try {
            $stmt = $this->connection->prepare($sql);
            
            // Bind parameters if provided
            if (!empty($params)) {
                $i = 1;
                foreach ($params as $param) {
                    if (is_int($param)) {
                        $stmt->bindValue($i, $param, SQLITE3_INTEGER);
                    } else {
                        $stmt->bindValue($i, $param, SQLITE3_TEXT);
                    }
                    $i++;
                }
            }
            
            $result = $stmt->execute();
            return $result;
        } catch (Exception $e) {
            error_log("SQLite query failed: " . $e->getMessage());
            return false;
        }
    }

    public function fetch($result) {
        if ($result) {
            return $result->fetchArray(SQLITE3_ASSOC);
        }
        return false;
    }

    public function fetchAll($result) {
        $rows = [];
        if ($result) {
            while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
                $rows[] = $row;
            }
        }
        return $rows;
    }

    public function lastInsertId() {
        return $this->connection->lastInsertRowID();
    }

    public function __destruct() {
        if ($this->connection) {
            $this->connection->close();
        }
    }
} 