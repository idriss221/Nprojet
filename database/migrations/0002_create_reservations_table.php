<?php

declare(strict_types=1);

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;











return function (): bool {
    if (Capsule::schema()->hasTable('reservations')) {
        return false;
    }

    Capsule::schema()->create('reservations', static function (Blueprint $table): void {
        $table->id();
        $table->foreignId('salle_id')
            ->constrained('salles')
            ->cascadeOnDelete();
        $table->string('responsable', 120);
        $table->string('email', 191);
        $table->string('motif', 255);
        $table->dateTime('date_debut');
        $table->dateTime('date_fin');
        $table->enum('statut', ['confirmée', 'annulée'])->default('confirmée');
        $table->timestamps();
    });

    return true;
};