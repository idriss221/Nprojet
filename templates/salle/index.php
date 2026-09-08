<?php

?>
<div class="barre-outils">
    <a class="bouton" href="/salles/create">Ajouter une salle</a>
</div>

<?php if ($salles === []): ?>
    <p class="vide">Aucune salle enregistrée.</p>
<?php else: ?>
    <div class="tableau-enveloppe">
        <table class="tableau">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Bâtiment</th>
                    <th>Capacité</th>
                    <th>Type</th>
                    <th>Statut</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($salles as $salle): ?>
                    <tr>
                        <td><a href="/salles/<?= (int) $salle->id ?>"><?= $this->e($salle->nom) ?></a></td>
                        <td><?= $this->e($salle->batiment) ?></td>
                        <td><?= (int) $salle->capacite ?></td>
                        <td><?= $this->e($salle->type) ?></td>
                        <td>
                            <?php if ($salle->active): ?>
                                <span class="badge badge-actif">Active</span>
                            <?php else: ?>
                                <span class="badge badge-inactif">Inactive</span>
                            <?php endif; ?>
                        </td>
                        <td class="actions">
                            <a href="/salles/<?= (int) $salle->id ?>/edit">Modifier</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
