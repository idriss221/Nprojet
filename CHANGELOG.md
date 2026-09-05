# Changelog

Toutes les modifications notables de ce projet sont documentées dans ce fichier.

## [Unreleased]

- Ajout des données initiales (seed) prévu.
- Modèles `Salle` et `Reservation` créés.

## [0.3.0] - 2026-09-05

### Ajouté

- `App\Model\Salle` : table `salles`, `$fillable`, conversions (`capacite` int,
  `active` bool, dates), relation `hasMany(Reservation)` → `$salle->reservations`.
- `App\Model\Reservation` : table `reservations`, `$fillable`, conversions
  (dates), constantes de statut, relation `belongsTo(Salle)` → `$reservation->salle`.

## [0.2.0] - 2026-09-05

### Ajouté

- `.env.example` : variables `APP_ENV`, `APP_DEBUG` et `DB_*` (MySQL).
- `config/database.php` : configuration unique de `Capsule\Manager`, démarrage
  d'Eloquent, chargement de `phpdotenv` (jamais de `getenv()` dans les classes métier).
- `database/migrate.php` : exécuteur de migrations (option `--fresh`), gestion
  d'erreurs de connexion avec message explicite.
- Migrations `0001_create_salles_table.php` et `0002_create_reservations_table.php`
  (schéma conforme au cahier des charges, idempotentes).

## [0.1.0] - 2026-09-05

### Ajouté

- Projet Composer initialisé (`composer.json`, `composer.lock`).
- Autoloading PSR-4 (`App\` → `src/`).
- Dépendances imposées installées : `nikic/fast-route`, `respect/validation`,
  `illuminate/database`, `php-di/php-di`, `vlucas/phpdotenv`.
- Arborescence du projet (`config/`, `database/`, `public/`, `routes/`, `src/`,
  `templates/`, `tests/`).
- Classe `App\Application` (Front Controller, point d'entrée unique).

## [0.0.0] - 2026-09-05

### Ajouté

- Dépôt Git initialisé, branche `main`.
- `.gitignore` : `vendor/`, `.env`, caches de tests, fichiers IDE.
- `README.md` : présentation du projet, dépendances imposées, prérequis.
- `CHANGELOG.md` : historique des versions (ce fichier).