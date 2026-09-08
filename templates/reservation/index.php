<?php


?>
<div class="barre-outils">
    <a class="bouton" href="/reservations/create">Nouvelle réservation</a>
</div>

<form class="filtre" method="get" action="/reservations">
    <label for="salle">Filtrer par salle</label>
    <select id="salle" name="salle" onchange="this.form.submit()">
        <option value="">Toutes les salles</option>
        <?php foreach ($salles as $salle): ?>
            <option value="<?= (int) $salle->id ?>" <?= (string) ($_GET['salle'] ?? '') === (string) $salle->id ? 'selected' : '' ?>>
                <?= $this->e($salle->nom) ?>
            </option>
        <?php endforeach; ?>
    </select>
</form>

<?php if ($reservations === []): ?>
    <p class="vide">Aucune réservation trouvée.</p>
<?php else: ?>
    <div class="tableau-enveloppe">
        <table class="tableau">
            <thead>
                <tr>
                    <th>Salle</th>
                    <th>Responsable</th>
                    <th>Motif</th>
                    <th>Début</th>
                    <th>Fin</th>
                    <th>Statut</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reservations as $reservation): ?>
                    <tr>
                        <td><a href="/reservations/<?= (int) $reservation->id ?>"><?= $this->e($reservation->salle->nom) ?></a></td>
                        <td><?= $this->e($reservation->responsable) ?></td>
                        <td><?= $this->e($reservation->motif) ?></td>
                        <td><?= $this->e($reservation->date_debut) ?></td>
                        <td><?= $this->e($reservation->date_fin) ?></td>
                        <td>
                            <?php if ($reservation->statut === 'confirmée'): ?>
                                <span class="badge badge-actif">Confirmée</span>
                            <?php else: ?>
                                <span class="badge badge-inactif">Annulée</span>
                            <?php endif; ?>
                        </td>
                        <td class="actions">
                            <?php if ($reservation->statut === 'confirmée'): ?>
                                <form method="post" action="/reservations/<?= (int) $reservation->id ?>/cancel" class="inline">
                                    <button type="submit" class="lien-supprimer">Annuler</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
