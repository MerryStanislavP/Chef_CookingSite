-- Таблиця для логування запитів
CREATE TABLE IF NOT EXISTS request_logs (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
    method TEXT NOT NULL,
    url TEXT NOT NULL,
    ip_address TEXT,
    user_agent TEXT,
    response_time INTEGER, -- час відповіді в мілісекундах
    status_code INTEGER,
    request_body TEXT,
    response_body TEXT
);

-- Таблиця для логування помилок
CREATE TABLE IF NOT EXISTS error_logs (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
    error_level TEXT NOT NULL, -- ERROR, WARNING, NOTICE, etc.
    error_message TEXT NOT NULL,
    error_file TEXT,
    error_line INTEGER,
    error_trace TEXT,
    request_id INTEGER,
    user_id INTEGER,
    FOREIGN KEY (request_id) REFERENCES request_logs(id)
);

-- Таблиця для логування активності користувачів
CREATE TABLE IF NOT EXISTS user_activity_logs (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
    user_id INTEGER NOT NULL,
    action_type TEXT NOT NULL, -- LOGIN, LOGOUT, VIEW_RECIPE, ADD_FAVORITE, etc.
    action_details TEXT,
    ip_address TEXT,
    request_id INTEGER,
    FOREIGN KEY (request_id) REFERENCES request_logs(id)
);

-- Індекси для оптимізації пошуку
CREATE INDEX IF NOT EXISTS idx_request_logs_timestamp ON request_logs(timestamp);
CREATE INDEX IF NOT EXISTS idx_error_logs_timestamp ON error_logs(timestamp);
CREATE INDEX IF NOT EXISTS idx_user_activity_logs_timestamp ON user_activity_logs(timestamp);
CREATE INDEX IF NOT EXISTS idx_user_activity_logs_user_id ON user_activity_logs(user_id); 