<?php

?>
<div class="barre-outils">
    <a class="bouton" href="/reservations/create">📅 Nouvelle réservation</a>
</div>

<form class="filtre" method="get" action="/reservations">
    <label for="salle"><strong>Filtrer par salle :</strong></label>
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
    <div class="etat-vide">
        <div class="etat-vide-icon">📭</div>
        <div class="etat-vide-titre">Aucune réservation trouvée</div>
        <div class="etat-vide-texte">Créez une nouvelle réservation pour commencer</div>
        <a class="bouton" href="/reservations/create">Créer une réservation</a>
    </div>
<?php else: ?>
    <div class="cartes-grille">
        <?php foreach ($reservations as $reservation): ?>
            <div class="carte">
                <div class="carte-entete">
                    <h3 class="carte-titre">🏢 <?= $this->e($reservation->salle->nom) ?></h3>
                    <div class="carte-sous-titre">
                        <span class="carte-badge <?= $reservation->statut === 'confirmée' ? 'confirmee' : 'annulee' ?>">
                            <?= $reservation->statut === 'confirmée' ? '✓ Confirmée' : '✗ Annulée' ?>
                        </span>
                    </div>
                </div>
                <div class="carte-corps">
                    <div class="carte-item">
                        <span class="carte-label">Responsable</span>
                        <span class="carte-valeur"><?= $this->e($reservation->responsable) ?></span>
                    </div>
                    <div class="carte-item">
                        <span class="carte-label">Motif</span>
                        <span class="carte-valeur"><?= $this->e($reservation->motif) ?></span>
                    </div>
                    <div class="carte-item">
                        <span class="carte-label">Début</span>
                        <span class="carte-valeur">📅 <?= $this->e($reservation->date_debut) ?></span>
                    </div>
                    <div class="carte-item">
                        <span class="carte-label">Fin</span>
                        <span class="carte-valeur">🕐 <?= $this->e($reservation->date_fin) ?></span>
                    </div>
                </div>
                <div class="carte-pied">
                    <a href="/reservations/<?= (int) $reservation->id ?>">Détails</a>
                    <?php if ($reservation->statut === 'confirmée'): ?>
                        <form method="post" action="/reservations/<?= (int) $reservation->id ?>/cancel" class="inline" style="flex: 1;">
                            <button type="submit" class="bouton bouton-danger" style="width: 100%; margin: 0;">Annuler</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
