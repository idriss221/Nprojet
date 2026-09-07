<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\Salle;




final class EloquentSalleRepository implements SalleRepositoryInterface
{
    public function all(): array
    {
        return Salle::orderBy('nom')->get()->all();
    }

    public function findById(int $id): ?Salle
    {
        return Salle::find($id);
    }

    public function create(array $donnees): Salle
    {
        return Salle::create($donnees);
    }
}