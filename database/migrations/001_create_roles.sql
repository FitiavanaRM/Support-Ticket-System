-- creation bd avec la table roles

CREATE DATABASE IF NOT EXISTS support_tickets CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE support_tickets;

CREATE TABLE IF NOT EXISTS roles (
    id TINYINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(20) NOT NULL UNIQUE,
    label VARCHAR(50) NOT NULL
) ENGINE=InnoDB;