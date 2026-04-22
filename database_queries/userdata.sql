CREATE TABLE userdata(
	id INT(11) NOT NULL AUTO_INCREMENT,
    user_id INT UNSIGNED NOT NULL,
    room VARCHAR(30) NOT NULL,
    device VARCHAR(30) NOT NULL,
    state VARCHAR(30) NOT NULL,
    set_routine INT(4) NOT NULL,
    energy_usage FLOAT NOT NULL,
    PRIMARY KEY (id),
    
	FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);