<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\CreerReservationDTO;
use App\Exception\SalleIndisponibleException;
use App\Model\Reservation;
use App\Repository\ReservationRepositoryInterface;
use App\Repository\SalleRepositoryInterface;

final class CreerReservationService
{
    public const DUREE_MAX_HEURES = 4;

    public function __construct(
        private readonly SalleRepositoryInterface $salles,
        private readonly ReservationRepositoryInterface $reservations
    ) {
    }

    public function creer(CreerReservationDTO $dto): Reservation
    {
        $salle = $this->salles->findById($dto->salleId);

        if ($salle === null) {
            throw new SalleIndisponibleException('La salle demandée n’existe pas.');
        }

        if (! $salle->active) {
            throw new SalleIndisponibleException(
                'La salle « ' . $salle->nom . ' » est inactive : réservation impossible.'
            );
        }

        $maintenant = new \DateTimeImmutable();

        if ($dto->dateDebut <= $maintenant) {
            throw new SalleIndisponibleException('La date de début doit être dans le futur.');
        }

        if ($dto->dateFin <= $dto->dateDebut) {
            throw new SalleIndisponibleException('La date de fin doit être après la date de début.');
        }

        $intervalle = $dto->dateDebut->diff($dto->dateFin);
        $dureeMinutes = $intervalle->days * 1440 + $intervalle->h * 60 + $intervalle->i;

        if ($dureeMinutes > self::DUREE_MAX_HEURES * 60) {
            throw new SalleIndisponibleException(
                sprintf('Une réservation ne peut pas dépasser %d heures.', self::DUREE_MAX_HEURES)
            );
        }

        if ($this->reservations->overlapping($dto->salleId, $dto->dateDebut, $dto->dateFin) !== []) {
            throw new SalleIndisponibleException('Cette salle est déjà réservée sur la période demandée.');
        }

        return $this->reservations->create([
            'salle_id'    => $dto->salleId,
            'responsable' => $dto->responsable,
            'email'       => $dto->email,
            'motif'       => $dto->motif,
            'date_debut'  => $dto->dateDebut->format('Y-m-d H:i:s'),
            'date_fin'    => $dto->dateFin->format('Y-m-d H:i:s'),
        ]);
    }
}
