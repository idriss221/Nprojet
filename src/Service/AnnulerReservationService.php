<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\ReservationIntrouvableException;
use App\Model\Reservation;
use App\Repository\ReservationRepositoryInterface;

final class AnnulerReservationService
{
    public function __construct(private readonly ReservationRepositoryInterface $reservations)
    {
    }

    public function toutes(): array
    {
        return $this->reservations->all();
    }

    public function retrouver(int $id): ?Reservation
    {
        return $this->reservations->findById($id);
    }

    public function pourSalle(int $salleId): array
    {
        return $this->reservations->forSalle($salleId);
    }

    public function annuler(int $id): void
    {
        if (! $this->reservations->cancel($id)) {
            throw new ReservationIntrouvableException('La réservation demandée est introuvable.');
        }
    }
}
