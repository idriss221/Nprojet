<?php



$anciennes = $anciennes !== [] ? $anciennes : [
    'salle_id'    => '',
    'responsable' => '',
    'email'       => '',
    'motif'       => '',
    'date_debut'  => '',
    'date_fin'    => '',
];
?>
<form class="formulaire" method="post" action="/reservations">
    <div class="champ <?= isset($erreurs['salle_id']) ? 'erreur' : '' ?>">
        <label for="salle_id">Salle</label>
        <select id="salle_id" name="salle_id" required>
            <option value="">— Choisir une salle —</option>
            <?php foreach ($salles as $salle): ?>
                <option value="<?= (int) $salle->id ?>" <?= (string) $anciennes['salle_id'] === (string) $salle->id ? 'selected' : '' ?>>
                    <?= $this->e($salle->nom) ?> (<?= (int) $salle->capacite ?> places)
                </option>
            <?php endforeach; ?>
        </select>
        <?php if (isset($erreurs['salle_id'])): ?><p class="message-erreur"><?= $this->e($erreurs['salle_id']) ?></p><?php endif; ?>
    </div>

    <div class="champ <?= isset($erreurs['responsable']) ? 'erreur' : '' ?>">
        <label for="responsable">Responsable</label>
        <input type="text" id="responsable" name="responsable" value="<?= $this->e($anciennes['responsable']) ?>" required>
        <?php if (isset($erreurs['responsable'])): ?><p class="message-erreur"><?= $this->e($erreurs['responsable']) ?></p><?php endif; ?>
    </div>

    <div class="champ <?= isset($erreurs['email']) ? 'erreur' : '' ?>">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="<?= $this->e($anciennes['email']) ?>" required>
        <?php if (isset($erreurs['email'])): ?><p class="message-erreur"><?= $this->e($erreurs['email']) ?></p><?php endif; ?>
    </div>

    <div class="champ <?= isset($erreurs['motif']) ? 'erreur' : '' ?>">
        <label for="motif">Motif</label>
        <input type="text" id="motif" name="motif" value="<?= $this->e($anciennes['motif']) ?>" required>
        <?php if (isset($erreurs['motif'])): ?><p class="message-erreur"><?= $this->e($erreurs['motif']) ?></p><?php endif; ?>
    </div>

    <div class="champ <?= isset($erreurs['date_debut']) ? 'erreur' : '' ?>">
        <label for="date_debut">Début</label>
        <input type="datetime-local" id="date_debut" name="date_debut" value="<?= $this->e($anciennes['date_debut']) ?>" required>
        <?php if (isset($erreurs['date_debut'])): ?><p class="message-erreur"><?= $this->e($erreurs['date_debut']) ?></p><?php endif; ?>
    </div>

    <div class="champ <?= isset($erreurs['date_fin']) ? 'erreur' : '' ?>">
        <label for="date_fin">Fin</label>
        <input type="datetime-local" id="date_fin" name="date_fin" value="<?= $this->e($anciennes['date_fin']) ?>" required>
        <?php if (isset($erreurs['date_fin'])): ?><p class="message-erreur"><?= $this->e($erreurs['date_fin']) ?></p><?php endif; ?>
    </div>

    <div class="actions-formulaire">
        <button type="submit" class="bouton">Créer la réservation</button>
        <a href="/reservations">Annuler</a>
    </div>
</form>
