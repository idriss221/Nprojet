<?php

?>
<div class="fiche">
    <div class="fiche-entete">
        <h2>Réservation #<?= (int) $reservation->id ?></h2>
        <?php if ($reservation->statut === 'confirmée'): ?>
            <span class="badge badge-actif">Confirmée</span>
        <?php else: ?>
            <span class="badge badge-inactif">Annulée</span>
        <?php endif; ?>
    </div>

    <dl class="detail">
        <dt>Salle</dt>
        <dd><a href="/salles/<?= (int) $reservation->salle->id ?>"><?= $this->e($reservation->salle->nom) ?></a></dd>
        <dt>Responsable</dt>
        <dd><?= $this->e($reservation->responsable) ?></dd>
        <dt>Email</dt>
        <dd><?= $this->e($reservation->email) ?></dd>
        <dt>Motif</dt>
        <dd><?= $this->e($reservation->motif) ?></dd>
        <dt>Début</dt>
        <dd><?= $this->e($reservation->date_debut) ?></dd>
        <dt>Fin</dt>
        <dd><?= $this->e($reservation->date_fin) ?></dd>
    </dl>

    <div class="barre-outils">
        <?php if ($reservation->statut === 'confirmée'): ?>
            <form method="post" action="/reservations/<?= (int) $reservation->id ?>/cancel" class="inline">
                <button type="submit" class="bouton bouton-danger">Annuler cette réservation</button>
            </form>
        <?php endif; ?>
        <a href="/reservations">Retour à la liste</a>
    </div>
</div>
