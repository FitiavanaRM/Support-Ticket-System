-- données de référence
USE support_tickets;

INSERT IGNORE INTO roles (code, label) VALUES
    ('CLIENT', 'Client'),
    ('AGENT', 'Agent'),
    ('SUPERVISOR', 'Superviseur'),
    ('ADMIN', 'Administrateur');

INSERT IGNORE INTO priorities (name, level) VALUES
    ('Critique', 1),
    ('Haute', 2),
    ('Moyenne', 3),
    ('Basse', 4);

INSERT IGNORE INTO categories (name, description) VALUES
    ('Reseau', 'Problemes de connectivite'),
    ('Logiciel', 'Bugs applicatifs'),
    ('Materiel', 'Panne materielle'),
    ('Compte', 'Acces, mots de passe, permissions');

INSERT IGNORE INTO assignment_settings (id, strategy_code) VALUES (1, 'MANUAL');