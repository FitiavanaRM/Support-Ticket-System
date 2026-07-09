-- table priorities
USE support_tickets;

CREATE TABLE IF NOT EXISTS priorities (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    level TINYINT UNSIGNED NOT NULL UNIQUE   -- 1=Critique ... 4=Basse
) ENGINE=InnoDB;