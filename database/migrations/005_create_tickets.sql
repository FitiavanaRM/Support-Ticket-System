-- table tickets
USE support_tickets;

CREATE TABLE IF NOT EXISTS tickets (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    subject VARCHAR(150) NOT NULL,
    description TEXT NOT NULL,
    client_id INT UNSIGNED NOT NULL,
    agent_id INT UNSIGNED NULL,
    category_id INT UNSIGNED NOT NULL,
    priority_id INT UNSIGNED NOT NULL,
    status ENUM('open','assigned','in_progress','resolved','closed') NOT NULL DEFAULT 'open',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    resolved_at DATETIME NULL,
    closed_at DATETIME NULL,

    CONSTRAINT fk_tickets_client FOREIGN KEY (client_id) REFERENCES users(id) ON DELETE RESTRICT,
    CONSTRAINT fk_tickets_agent FOREIGN KEY (agent_id) REFERENCES users(id) ON DELETE SET NULL,
    CONSTRAINT fk_tickets_category FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT,
    CONSTRAINT fk_tickets_priority FOREIGN KEY (priority_id) REFERENCES priorities(id) ON DELETE RESTRICT,

    INDEX idx_tickets_status (status),
    INDEX idx_tickets_client (client_id),
    INDEX idx_tickets_agent (agent_id)
) ENGINE=InnoDB;