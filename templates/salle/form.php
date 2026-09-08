<?php



$id = $salle?->id;
$anciennes = $anciennes !== [] ? $anciennes : [
    'nom'      => $salle?->nom ?? '',
    'batiment' => $salle?->batiment ?? '',
    'capacite' => $salle?->capacite ?? '',
    'type'     => $salle?->type ?? '',
    'active'   => $salle?->active ?? true,
];
$types = ['cours', 'informatique', 'laboratoire', 'amphitheatre', 'reunion'];
$chemin = $id === null ? '/salles' : '/salles/' . $id . '/edit';
?>
<form class="formulaire" method="post" action="<?= $chemin ?>">
    <div class="champ <?= isset($erreurs['nom']) ? 'erreur' : '' ?>">
        <label for="nom">Nom</label>
        <input type="text" id="nom" name="nom" value="<?= $this->e($anciennes['nom']) ?>" required>
        <?php if (isset($erreurs['nom'])): ?><p class="message-erreur"><?= $this->e($erreurs['nom']) ?></p><?php endif; ?>
    </div>

    <div class="champ <?= isset($erreurs['batiment']) ? 'erreur' : '' ?>">
        <label for="batiment">Bâtiment</label>
        <input type="text" id="batiment" name="batiment" value="<?= $this->e($anciennes['batiment']) ?>" required>
        <?php if (isset($erreurs['batiment'])): ?><p class="message-erreur"><?= $this->e($erreurs['batiment']) ?></p><?php endif; ?>
    </div>

    <div class="champ <?= isset($erreurs['capacite']) ? 'erreur' : '' ?>">
        <label for="capacite">Capacité</label>
        <input type="number" id="capacite" name="capacite" min="1" max="1000" value="<?= (int) $anciennes['capacite'] ?>" required>
        <?php if (isset($erreurs['capacite'])): ?><p class="message-erreur"><?= $this->e($erreurs['capacite']) ?></p><?php endif; ?>
    </div>

    <div class="champ <?= isset($erreurs['type']) ? 'erreur' : '' ?>">
        <label for="type">Type</label>
        <select id="type" name="type">
            <?php foreach ($types as $t): ?>
                <option value="<?= $t ?>" <?= $anciennes['type'] === $t ? 'selected' : '' ?>><?= $this->e($t) ?></option>
            <?php endforeach; ?>
        </select>
        <?php if (isset($erreurs['type'])): ?><p class="message-erreur"><?= $this->e($erreurs['type']) ?></p><?php endif; ?>
    </div>

    <div class="champ">
        <label>
            <input type="checkbox" name="active" value="1" <?= ! empty($anciennes['active']) ? 'checked' : '' ?>>
            Salle active
        </label>
    </div>

    <div class="actions-formulaire">
        <button type="submit" class="bouton"><?= $id === null ? 'Créer' : 'Enregistrer' ?></button>
        <a href="/salles">Annuler</a>
    </div>
</form>
