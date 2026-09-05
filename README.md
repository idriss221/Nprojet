# Gestion des réservations de salles universitaires

Application web de consultation des salles et de gestion de leurs réservations,
développée en **PHP orienté objet** (sans framework complet), avec **MySQL** et
des composants spécialisés installés via **Composer**.

## Dépendances imposées

| Besoin                | Dépendance                    |
| --------------------- | ----------------------------- |
| Routeur               | `nikic/fast-route`            |
| Validation            | `respect/validation` ^2.4     |
| ORM (Active Record)   | `illuminate/database` ^12.0   |
| Conteneur d'injection | `php-di/php-di` ^7.0          |
| Variables d'environnement | `vlucas/phpdotenv`         |

## État du projet

Ce dépôt suit un découpage en étapes versionnées (branches `feature/*` et tags
`sujets`). Voir `CHANGELOG.md` pour l'historique détaillé.

## Prérequis

- PHP 8.2 ou 8.3 (extension `pdo_mysql` activée)
- Composer
- MySQL (ou compatible)

## Installation

```bash
composer install
cp .env.example .env
# modifier .env selon sa configuration locale MySQL
```

## Création de la base et lancement

Voir les scripts dans `database/` et le `README` complété à la fin du projet.