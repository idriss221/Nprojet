<?php

?>
<div class="fiche">
    <div class="fiche-entete">
        <h2><?= $this->e($salle->nom) ?></h2>
        <?php if ($salle->active): ?>
            <span class="badge badge-actif">Active</span>
        <?php else: ?>
            <span class="badge badge-inactif">Inactive</span>
        <?php endif; ?>
    </div>

    <dl class="detail">
        <dt>Bâtiment</dt>
        <dd><?= $this->e($salle->batiment) ?></dd>
        <dt>Capacité</dt>
        <dd><?= (int) $salle->capacite ?> places</dd>
        <dt>Type</dt>
        <dd><?= $this->e($salle->type) ?></dd>
    </dl>

    <div class="barre-outils">
        <a class="bouton" href="/salles/<?= (int) $salle->id ?>/edit">Modifier</a>
        <a href="/salles">Retour à la liste</a>
    </div>
</div>
