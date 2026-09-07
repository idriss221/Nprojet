# Réponses aux questions — Étapes 0 à 4

Réponses pédagogiques aux questions posées par le cahier des charges pour le
travail de samedi matin (versions `v0.0.0` → `v0.4.0`).

---

## Étape 1 — Projet Composer

### 1. Quel est le rôle de Composer ?

Composer est le **gestionnaire de dépendances** de PHP (équivalent de `npm` en
JavaScript). Il lit `composer.json`, télécharge les bibliothèques dans
`vendor/`, résout les versions compatibles et génère un **autoloader** qui
charge automatiquement les classes (`App\` → `src/`) sans écrire une ligne de
`require` à la main.

### 2. Quelle différence existe entre `require` et `require-dev` ?

- `require` : dépendances **nécessaires en production** (routeur, ORM, conteneur…).
- `require-dev` : dépendances **utiles uniquement en développement** (PHPUnit,
  outil de debug, linter…).

En production on peut installer sans les dépendances dev :
`composer install --no-dev`.

### 3. Pourquoi faut-il versionner `composer.lock` ?

`composer.lock` fige les **versions exactes** installées. Deux développeurs (ou
le déploiement) qui lance `composer install` obtiennent ainsi exactement les
mêmes versions : l'application devient **reproductible** et il n'y a pas de
surprise (« ça marchait chez moi »).

### 4. Pourquoi ne versionne-t-on pas `vendor/` ?

`vendor/` contient des bibliothèques écrites par d'autres, potentiellement très
lourdes, et **reconstruisibles** à tout moment avec `composer install`.
Versionner ce dossier alourdirait inutilement le dépôt et mélangerait le code
tiers au code source du projet.

---

## Étape 2 — Configuration d'Eloquent

### 1. Quel rôle joue Capsule\Manager ?

`Capsule\Manager` est le **noyau d'Eloquent détaché de Laravel**. Sans lui,
Eloquent a besoin de tout le framework (config, événements, conteneur) pour
fonctionner. Avec Capsule, on déclare la connexion, on appelle
`setAsGlobal()` (accès direct type `Capsule::table()`) et `bootEloquent()`
(activation de l'Active Record). C'est lui qui fait « tourner » Eloquent dans
une application sans framework.

### 2. Pourquoi Eloquent peut-il fonctionner sans Laravel ?

Parce que les paquets `illuminate/*` sont **autonomes** : `illuminate/database`
contient l'ORM, le Query Builder et le Schema Builder. Le framework Laravel
n'est qu'une « superstructure » qui fournit ces composants ; ici, Capsule prend
en charge ce rôle de démarrage.

### 3. Où doit se trouver le démarrage de l'ORM ?

Dans un fichier de **configuration unique** (`config/database.php`), chargé une
seule fois via `require_once`. Les scripts (migrations, seed, application)
incluent ce fichier au lieu de re-configurer la connexion chacun à leur tour —
c'est la contrainte « la connexion doit être configurée une seule fois ».

### 4. Quelle différence existe entre ORM et SQL écrit à la main ?

- **ORM** : on manipule des **objets** (`Salle::where(...)->get()`). Le SQL est
  généré automatiquement, adapté au SGBD, et les résultats sont convertis en
  objets typés.
- **SQL à la main** : on rédige les requêtes (`SELECT * FROM salles`) et on
  convertit les lignes soi-même. Plus verbeux, plus sujet aux erreurs et sans
  portabilité d'un SGBD à l'autre.

---

## Étape 3 — Les modèles

### 1. Quel type de relation Eloquent avez-vous utilisé ?

Une relation de type **one-to-many (1,n)** :

- `Salle::reservations()` → `hasMany(Reservation::class, 'salle_id')`;
- `Reservation::salle()` → `belongsTo(Salle::class, 'salle_id')`.

Ainsi `$salle->reservations` retourne la collection des réservations de la
salle et `$reservation->salle` retourne la salle associée.

### 2. Pourquoi déclarer `$fillable` ou `$guarded` ?

C'est une **protection contre l'assignation massive** : si un utilisateur envoie
un champ supplémentaire dans un formulaire (par exemple `id`, `statut` ou
`admin`), Eloquent ne l'assignera pas au modèle. `$fillable` liste les champs
autorisés explicitement, ce qui est plus sûr que `$guarded = ['*']` quand on
veut être précis.

### 3. Pourquoi convertir `active` en booléen ?

En MySQL, `BOOLEAN` est un alias de `TINYINT(1)` : la valeur est stockée 0/1.
Sans conversion, on récupérerait un entier (`0` ou `1`) dans PHP. Avec le cast
`'active' => 'boolean'`, Eloquent retourne un vrai booléen (`true`/`false`),
exploitable directement dans des conditions (`if ($salle->active)`).

### 4. Pourquoi convertir les dates en objets ?

Les dates SQL sont des chaînes (`"2026-09-06 10:00:00"`). Avec le cast en
`datetime`, Eloquent retourne des **objets `Carbon`** : on peut alors comparer,
formater, calculer des durées (`->diffInHours()`) et manipuler les dates sans
parsing manuel fragile.

---

## Étape 4 — Données initiales (seed)

### 1. Quelle différence existe entre migration et seeder ?

- **Migration** : décrit la **structure** de la base (création/modification des
  tables, colonnes, clés étrangères).
- **Seeder** : ajoute des **données** dans ces tables (salles de référence,
  jeux de test…).

On migre une fois (structure) puis on seed (données) — on peut exécuter le seed
plusieurs fois, la structure reste inchangée.

### 2. Pourquoi les données initiales doivent-elles être reproductibles ?

Pour que chaque environnement (machine de l'étudiant, machine du professeur,
serveur) obtienne **le même jeu de données de départ** après exécution. Si le
seed dépend de l'état précédent de la base ou crée des doublons, les données
deviennent incohérentes et les résultats d'un dev à l'autre divergent.

### 3. Comment empêcher les doublons ?

En s'appuyant sur une **clé d'unicité naturelle** — ici le nom de la salle — et
sur `firstOrCreate()` :

```php
Salle::firstOrCreate(['nom' => $donnees['nom']], $donnees);
```

Eloquent cherche d'abord une salle portant ce nom : si elle existe, il la
retourne sans rien créer ; sinon, il la crée avec les données fournies.---

## Étape 5 — La validation

### 1. Pourquoi séparer la validation syntaxique des règles métier ?

Parce qu'elles n'ont **pas la même nature ni le même emplacement** :

- **syntaxique** (forme) : « l'e-mail a-t-il un format valide ? », « le motif
  fait-il entre 5 et 255 caractères ? » → dans les validateurs.
- **métier** (fond) : « la salle est-elle libre pendant cette période ? »,
  « la durée dépasse-t-elle 4 h ? », « la date est-elle dans le futur ? » →
  dans les services.

Une validation **syntaxique** ne nécessite aucune base de données : elle ne
vérifie que la forme des données. Une règle **métier** au contraire a besoin de
contexte (par exemple interroger les réservations existantes pour détecter un
chevauchement). Les séparer permet de tester chacune indépendamment et de
garder les validateurs purs de toute dépendance.

### 2. Pourquoi créer une interface de validation ?

Pour que tous les validateurs aient une **signature commune**
(`validate(array $data): ValidationResult`). Le contrôleur peut alors utiliser
n'importe quel validateur (`SalleValidator` ou `ReservationValidator`) de la
même façon, sans connaître la classe précise. Cela respecte le principe
d'inversion des dépendances : on dépend d'un contrat, pas d'une implémentation.

### 3. Pourquoi le validateur ne doit-il pas enregistrer les données ?

Parce que la validation est une **lecture** sans **effet de bord**. Si le
validateur sauvegardait en base, il faudrait lui fournir le repository (ce qui
le complexifie), et il échouerait dès qu'on voudrait valider des données sans
persister (pré-remplissage d'un formulaire, tests). Enregistrer est le rôle du
**service**, qui appelle le repository APRÈS une validation réussie.

### 4. Comment retourner plusieurs erreurs en une seule fois ?

Avec Respect\Validation, on décrit toutes les règles attendues via `v::key()` :

```php
$validator = v::key('motif', v::notEmpty()->length(5, 255)->setName('motif'))
    ->key('email', v::notEmpty()->email()->setName('email'));
```

Puis `assert($data)` lève une `NestedValidationException` dont
`getMessages()` retourne un **tableau champ → message** pour TOUTES les erreurs
d'un coup. Le `ValidationResult` les capture et les expose via `errors()`.

