CREATE TABLE IF NOT EXISTS routines (
    id INT(11) NOT NULL AUTO_INCREMENT,
    user_id INT UNSIGNED NOT NULL,
    routine_name VARCHAR(100) NOT NULL,
    devices JSON,
    action VARCHAR(20) NOT NULL,
    scheduled_time TIME NOT NULL,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
