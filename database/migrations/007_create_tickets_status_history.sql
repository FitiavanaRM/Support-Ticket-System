-- table ticket_status_history
USE support_tickets;

CREATE TABLE IF NOT EXISTS ticket_status_history (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ticket_id INT UNSIGNED NOT NULL,
    from_status ENUM('open','assigned','in_progress','resolved','closed') NULL,
    to_status ENUM('open','assigned','in_progress','resolved','closed') NOT NULL,
    changed_by INT UNSIGNED NOT NULL,
    changed_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_history_ticket FOREIGN KEY (ticket_id) REFERENCES tickets(id) ON DELETE CASCADE,
    CONSTRAINT fk_history_user FOREIGN KEY (changed_by) REFERENCES users(id) ON DELETE RESTRICT,

    INDEX idx_history_ticket (ticket_id)
) ENGINE=InnoDB;