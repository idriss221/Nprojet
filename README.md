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
`sujets`). Voir `CHANGELOG.md` pour l'historique détaillé et `REPONSES.md` pour
les réponses aux questions des étapes.

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
## Déploiement

### Application en ligne (lien principal)

**👉 https://web-production-1b1bd.up.railway.app/**

L'application est déployée et accessible via HTTPS. Lien GitHub Pages :

**👉 https://idriss221.github.io/Nprojet/**

### Liens de déploiement

**Deux déploiements distincts et fonctionnels :**

| Déploiement | Source | URL (en ligne) | État |
| ----------- | ------ | -------------- | ---- |
| 🔵 **Code** | GitHub (branche `main`, dernière version) | https://web-production-1b1bd.up.railway.app/ | ✅ HTTP 200 |
| 🐳 **Image** | Docker Hub (`idriss45/gestion_de_salle_universitaire1:v0.12.0`) | https://web-image-production-9e7d.up.railway.app/ | ✅ HTTP 200 |

Autres accès :

| Type | Lien |
| ---- | ---- |
| Release GitHub `v0.12.0` | https://github.com/idriss221/Nprojet/releases/tag/v0.12.0 |
| Accès GitHub (Pages → appli) | https://idriss221.github.io/Nprojet/ |
| Image Docker Hub | https://hub.docker.com/r/idriss45/gestion_de_salle_universitaire1/tags?name=v0.12.0 |

Récupération de l'image :

```bash
docker pull idriss45/gestion_de_salle_universitaire1:v0.12.0
```
