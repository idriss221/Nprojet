<?php

declare(strict_types=1);

namespace App\Tests\Unit\Fakes;

use App\Model\Salle;
use App\Repository\SalleRepositoryInterface;

/**
 * Implémentation EN MÉMOIRE de SalleRepositoryInterface, utilisée par les
 * tests unitaires pour ne jamais toucher MySQL.
 */
final class InMemorySalleRepository implements SalleRepositoryInterface
{
    /** @var array<int, Salle> */
    private array $salles = [];

    /** @var list<array<string, mixed>> */
    private array $created = [];

    /** Pré-remplit le dépôt avec une salle existante (fixture). */
    public function add(Salle $salle): void
    {
        $this->salles[(int) $salle->id] = $salle;
    }

    public function all(): array
    {
        return array_values($this->salles);
    }

    public function findById(int $id): ?Salle
    {
        return $this->salles[$id] ?? null;
    }

    public function create(array $donnees): Salle
    {
        $salle = new Salle();
        $salle->id = (int) ($donnees['id'] ?? count($this->salles) + 1);
        $salle->nom = (string) ($donnees['nom'] ?? '');
        $salle->batiment = (string) ($donnees['batiment'] ?? '');
        $salle->capacite = (int) ($donnees['capacite'] ?? 0);
        $salle->type = (string) ($donnees['type'] ?? '');
        $salle->active = (bool) ($donnees['active'] ?? true);

        $this->created[] = $donnees;
        $this->salles[(int) $salle->id] = $salle;

        return $salle;
    }

    public function update(int $id, array $donnees): bool
    {
        if (! isset($this->salles[$id])) {
            return false;
        }

        $salle = $this->salles[$id];
        foreach ($donnees as $cle => $valeur) {
            $salle->{$cle} = $valeur;
        }

        return true;
    }

    /** Données passées à create() (pour vérifier ce qui a été persisté). */
    public function createdSalles(): array
    {
        return $this->created;
    }
}