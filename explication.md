# Explication ligne par ligne du modèle `Salle`

Voici l'analyse complète et imagée de chaque ligne de code de votre fichier.

---

### 1. Configuration et Sécurité

```php
<?php
```
* **En clair :** C'est le signal de départ. Cela indique à l'ordinateur que le texte qui suit n'est pas du texte ordinaire, mais du code écrit en langage **PHP**.

```php
declare(strict_types=1);
```
* **En clair :** Active le "mode strict" pour les types de données. Si le code attend un numéro (un entier) et que vous lui donnez du texte (comme `"20"` au lieu de `20`), l'ordinateur s'arrête net et affiche une erreur au lieu de deviner. C'est une **ceinture de sécurité** pour éviter les bugs.

```php
namespace App\Model;
```
* **En clair :** Crée une boîte de rangement virtuelle appelée `App\Model` pour y stocker ce fichier. Si un autre développeur crée une classe nommée `Salle` ailleurs dans le projet, les deux fichiers ne se mélangeront pas. C'est l'équivalent d'un **nom de famille** pour le code.

---

### 2. Importation des outils (Dépendances)

```php
use Illuminate\Database\Eloquent\Model;
```
* **En clair :** Va chercher la boîte à outils principale de Laravel (nommée `Model`). Grâce à elle, notre fichier va hériter automatiquement de dizaines de fonctions magiques pour parler à la base de données (sauvegarder, supprimer, chercher) sans qu'on ait à écrire de requêtes SQL complexes.

```php
use Illuminate\Database\Eloquent\Relations\HasMany;
```
* **En clair :** Importe un outil spécifique appelé `HasMany` (qui signifie *"Possède plusieurs"*). Cet outil sert à lier nos tables entre elles, comme pour dire qu'une salle possède plusieurs réservations.

---

### 3. Déclaration de la classe

```php
class Salle extends Model
```
* **En clair :** On fabrique un moule de programmation nommé `Salle`. Le mot `extends Model` signifie qu'on l'accouple à la boîte à outils importée plus haut : notre classe `Salle` devient officiellement un **Modèle de base de données**.

---

### 4. Paramétrage de la table et sécurité

```php
protected \$table = 'salles';
```
* **En clair :** Par défaut, Laravel cherche une table qui s'appelle le pluriel du mot (ici `salles`). Cette ligne confirme explicitement à l'ordinateur : *"Va chercher et stocke les données dans la table nommée `salles`"*.

```php
protected \$fillable = [
    'nom',
    'batiment',
    'capacite',
    'type',
    'active',
];
```
* **En clair :** C'est la **liste blanche** des informations qu'on a le droit de modifier ou de remplir d'un seul coup (par exemple via un formulaire). Si un utilisateur malveillant essaie d'injecter une donnée cachée (comme `est_administrateur`), Laravel va bloquer l'action car elle n'est pas écrite dans ce tableau.

---

### 5. Nettoyage et conversion des données (Casts)

```php
protected \$casts = [
```
* **En clair :** Ouvre un tableau de "traduction". Parfois, la base de données renvoie des chiffres ou des dates sous forme de texte brut. Ce bloc force la conversion dans le bon format dès que le code réceptionne la donnée.

```php
    'capacite' => 'integer',
```
* **En clair :** Transforme la capacité d'accueil de la salle en **nombre entier** propre (ex: `50`), ce qui permet de faire des calculs mathématiques dessus plus tard.

```php
    'active' => 'boolean',
```
* **En clair :** Transforme le statut de la salle en **Vrai ou Faux** (`true` ou `false`). C'est plus simple pour savoir si la salle est disponible ou fermée.

```php
    'created_at' => 'datetime',
    'updated_at' => 'datetime',
];
```
* **En clair :** Transforme les textes de dates de création et de mise à jour en véritables objets **Date et Heure**. Grâce à cela, on pourra facilement faire des opérations comme *"Ajouter 2 heures à la date"*.

---

### 6. Liaison entre les données (Relation Eloquent)

```php
public function reservations(): HasMany
{
```
* **En clair :** Crée une fonction (une action) nommée `reservations`. Le morceau `: HasMany` est une indication pour l'ordinateur : cette fonction va obligatoirement retourner une relation de type *"Possède plusieurs"*.

```php
    return \$this->hasMany(Reservation::class, 'salle_id');
}
```
* **En clair :** C'est l'ordre concret donné à Laravel : *"Quand je te demande les réservations d'une salle, va dans la table des Réservations et trouve toutes les lignes qui possèdent l'identifiant de ma salle dans leur colonne `salle_id`"*.
