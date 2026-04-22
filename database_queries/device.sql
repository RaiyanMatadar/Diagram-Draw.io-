CREATE TABLE IF NOT EXISTS devices (
    id INT(11) AUTO_INCREMENT,
    user_id INT UNSIGNED NOT NULL,
    room_id INT(11) NOT NULL,
    device_name VARCHAR(50) NOT NULL,
    device_type VARCHAR(30) NOT NULL,
    state VARCHAR(20) DEFAULT 'Off',
    features JSON,
    is_connected TINYINT(1) DEFAULT 0,
    energy_usage FLOAT DEFAULT 0,
    hours_used FLOAT DEFAULT 0,
    malfunction TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY(id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;