<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\Reservation;
use DateTimeInterface;







final class EloquentReservationRepository implements ReservationRepositoryInterface
{
    public function create(array $donnees): Reservation
    {
        return Reservation::create($donnees);
    }

    public function findById(int $id): ?Reservation
    {
        return Reservation::find($id);
    }

    public function all(): array
    {
        return Reservation::orderBy('date_debut')->get()->all();
    }

    public function forSalle(int $salleId): array
    {
        return Reservation::where('salle_id', $salleId)
            ->orderBy('date_debut')
            ->get()
            ->all();
    }

    public function overlapping(int $salleId, DateTimeInterface $debut, DateTimeInterface $fin): array
    {
        return Reservation::where('salle_id', $salleId)
            ->where('statut', Reservation::STATUT_CONFIRMEE)
            ->where('date_debut', '<', $fin->format('Y-m-d H:i:s'))
            ->where('date_fin', '>', $debut->format('Y-m-d H:i:s'))
            ->orderBy('date_debut')
            ->get()
            ->all();
    }

    public function cancel(int $id): bool
    {
        $reservation = Reservation::find($id);

        if ($reservation === null) {
            return false;
        }

        return (bool) $reservation->update(['statut' => Reservation::STATUT_ANNULEE]);
    }
}