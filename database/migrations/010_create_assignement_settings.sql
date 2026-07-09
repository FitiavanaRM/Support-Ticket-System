-- table assignment_settings
USE support_tickets;

CREATE TABLE IF NOT EXISTS assignment_settings (
    id TINYINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    strategy_code VARCHAR(30) NOT NULL,
    last_agent_id INT UNSIGNED NULL,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_settings_last_agent FOREIGN KEY (last_agent_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;