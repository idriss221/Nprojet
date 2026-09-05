<?php

declare(strict_types=1);












include_once __DIR__ . '/../config/database.php';

use Illuminate\Database\Capsule\Manager as Capsule;

$fresh = in_array('--fresh', $argv ?? [], true);

try {
    
    Capsule::connection()->getPdo()->query('SELECT 1 FROM DUAL');

    if ($fresh) {
        Capsule::schema()->dropIfExists('reservations');
        Capsule::schema()->dropIfExists('salles');
        echo "[fresh] Tables existantes supprimées.\n";
    }

    $files = glob(__DIR__ . '/migrations/*.php');
    sort($files, SORT_NATURAL);

    foreach ($files as $file) {
        $migration = require $file;
        $name = basename($file);

        if ($migration()) {
            echo "[ok]   {$name}\n";
        } else {
            echo "[skip] {$name} — table déjà présente\n";
        }
    }

    echo "\nMigrations terminées.\n";
} catch (Throwable $e) {
    fwrite(STDERR, "\nErreur de connexion à la base de données :\n");
    fwrite(STDERR, '  ' . $e->getMessage() . "\n\n");
    fwrite(STDERR, "Vérifiez les valeurs du fichier .env (DB_HOST, DB_PORT, DB_DATABASE,\n");
    fwrite(STDERR, "DB_USERNAME, DB_PASSWORD) puis relancez : php database/migrate.php\n");
    exit(1);
}