<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\CreerSalleDTO;
use App\Exception\ReservationIntrouvableException;
use App\Model\Salle;
use App\Repository\SalleRepositoryInterface;

final class SalleService
{
    public function __construct(private readonly SalleRepositoryInterface $salles)
    {
    }

    public function toutes(): array
    {
        return $this->salles->all();
    }

    public function retrouver(int $id): ?Salle
    {
        return $this->salles->findById($id);
    }

    public function creer(CreerSalleDTO $dto): Salle
    {
        return $this->salles->create([
            'nom'      => $dto->nom,
            'batiment' => $dto->batiment,
            'capacite' => $dto->capacite,
            'type'     => $dto->type,
            'active'   => $dto->active,
        ]);
    }

    public function modifier(int $id, CreerSalleDTO $dto): void
    {
        if (! $this->salles->update($id, [
            'nom'      => $dto->nom,
            'batiment' => $dto->batiment,
            'capacite' => $dto->capacite,
            'type'     => $dto->type,
            'active'   => $dto->active,
        ])) {
            throw new ReservationIntrouvableException('La salle demandée est introuvable.');
        }
    }
}
