<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\ReservationIntrouvableException;
use App\Repository\ReservationRepositoryInterface;
use App\Repository\SalleRepositoryInterface;

final class AnnulerReservationService
{
    public function __construct(private readonly ReservationRepositoryInterface $reservations)
    {
    }

    public function annuler(int $id): void
    {
        if (! $this->reservations->cancel($id)) {
            throw new ReservationIntrouvableException('La réservation demandée est introuvable.');
        }
    }
}
