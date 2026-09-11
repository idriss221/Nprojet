<?php

?>
<div class="barre-outils">
    <a class="bouton" href="/salles/create">➕ Ajouter une salle</a>
</div>

<?php if ($salles === []): ?>
    <div class="etat-vide">
        <div class="etat-vide-icon">📭</div>
        <div class="etat-vide-titre">Aucune salle enregistrée</div>
        <div class="etat-vide-texte">Commencez par ajouter une nouvelle salle pour gérer vos réservations</div>
        <a class="bouton" href="/salles/create">Créer une salle</a>
    </div>
<?php else: ?>
    <div class="cartes-grille">
        <?php foreach ($salles as $salle): ?>
            <div class="carte">
                <div class="carte-entete">
                    <h3 class="carte-titre"><?= $this->e($salle->nom) ?></h3>
                    <div class="carte-sous-titre">
                        <span class="carte-badge <?= $salle->active ? 'actif' : 'inactif' ?>">
                            <?= $salle->active ? '✓ Active' : '✗ Inactive' ?>
                        </span>
                    </div>
                </div>
                <div class="carte-corps">
                    <div class="carte-item">
                        <span class="carte-label">Bâtiment</span>
                        <span class="carte-valeur"><?= $this->e($salle->batiment) ?></span>
                    </div>
                    <div class="carte-item">
                        <span class="carte-label">Capacité</span>
                        <span class="carte-valeur">👥 <?= (int) $salle->capacite ?> personnes</span>
                    </div>
                    <div class="carte-item">
                        <span class="carte-label">Type</span>
                        <span class="carte-valeur"><?= $this->e($salle->type) ?></span>
                    </div>
                </div>
                <div class="carte-pied">
                    <a href="/salles/<?= (int) $salle->id ?>">Détails</a>
                    <a href="/salles/<?= (int) $salle->id ?>/edit">Modifier</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
