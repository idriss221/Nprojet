<?php

declare(strict_types=1);

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;









return function (): bool {
    if (Capsule::schema()->hasTable('salles')) {
        return false;
    }

    Capsule::schema()->create('salles', static function (Blueprint $table): void {
        $table->id();
        $table->string('nom', 100);
        $table->string('batiment', 100);
        $table->unsignedInteger('capacite');
        $table->enum('type', ['cours', 'informatique', 'laboratoire', 'amphitheatre', 'reunion']);
        $table->boolean('active')->default(true);
        $table->timestamps();
    });

    return true;
};