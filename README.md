# Support Ticket System

Projet de gestion de tickets de support.

## Devellopee par
HERIFITIAVANA Rotsy Nomena
Matricule - 30245
L2


## Description

Ce projet permet de gérer des tickets de support, d’assigner des agents, de suivre l’évolution des demandes et d’offrir une interface simple pour les utilisateurs, les agents et les administrateurs.

## Fonctionnalités

- Authentification et inscription des utilisateurs
- Création, consultation et suivi des tickets
- Gestion des catégories, priorités et statuts
- Assignation manuelle ou automatisée des tickets
- Tableau de bord avec statistiques et activité récente
- Interface web basée sur PHP, Bootstrap et une architecture MVC

## Stack technique

- PHP 8+
- PDO / MySQL
- Bootstrap 5
- PHPUnit pour les tests
- Architecture MVC personnalisée

## Structure du projet

- src/ : contrôleurs, services, modèles, repositories et vues
- public/ : point d’entrée web et assets front
- database/ : migrations et seeders
- tests/ : tests unitaires et de vues

## Installation

1. Cloner le projet
2. Installer les dépendances avec Composer :
   ```bash
   composer install
   ```
3. Créer la base de données et exécuter les migrations :
   ```bash
   php database/migrate.php
   ```
4. Peupler la base avec les données de démonstration :
   ```bash
   php database/seed.php
   ```
5. Démarrer le serveur local :
   ```bash
   php -S localhost:8000 -t public
   ```

## Utilisation

Ouvrez l’application dans votre navigateur à l’adresse :

```text
http://localhost:8000
```

## Comptes de démonstration

Les données de seed créent plusieurs comptes de test :

- client@demo.test / Password123!
- agent@demo.test / Password123!
- superviseur@demo.test / Password123!
- admin@demo.test / Password123!

Le client ne peut pas voir les listes des utilisateurs et l'assignement