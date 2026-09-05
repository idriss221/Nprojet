<?php

declare(strict_types=1);












include_once __DIR__ . '/../config/database.php';

use App\Model\Salle;

$salles = [
    [
        'nom'      => 'Amphithéâtre A',
        'batiment' => 'Bâtiment principal',
        'capacite' => 250,
        'type'     => 'amphitheatre',
        'active'   => true,
    ],
    [
        'nom'      => 'Salle B12',
        'batiment' => 'Bâtiment B',
        'capacite' => 40,
        'type'     => 'cours',
        'active'   => true,
    ],
    [
        'nom'      => 'Laboratoire Chimie',
        'batiment' => 'Bâtiment C',
        'capacite' => 24,
        'type'     => 'laboratoire',
        'active'   => true,
    ],
    [
        'nom'      => 'Salle Informatique 1',
        'batiment' => 'Bâtiment B',
        'capacite' => 30,
        'type'     => 'informatique',
        'active'   => true,
    ],
    [
        'nom'      => 'Salle de réunion',
        'batiment' => 'Bâtiment principal',
        'capacite' => 12,
        'type'     => 'reunion',
        'active'   => true,
    ],
];

try {
    foreach ($salles as $donnees) {
        
        $salle = Salle::firstOrCreate(
            ['nom' => $donnees['nom']],
            $donnees
        );

        echo sprintf(
            "[%s] %s (capacité : %d, type : %s)\n",
            $salle->wasRecentlyCreated ? 'créée' : 'déjà présente',
            $salle->nom,
            $salle->capacite,
            $salle->type
        );
    }

    echo "\nSeed terminé. Nombre de salles en base : " . Salle::count() . "\n";
} catch (Throwable $e) {
    fwrite(STDERR, "\nErreur lors du seed :\n");
    fwrite(STDERR, '  ' . $e->getMessage() . "\n\n");
    fwrite(STDERR, "Vérifiez le fichier .env et que les tables existent\n");
    fwrite(STDERR, "(lancez d'abord : php database/migrate.php).\n");
    exit(1);
}