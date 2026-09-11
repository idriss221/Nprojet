<?php

declare(strict_types=1);

namespace App\Tests\Unit\Fakes;

use App\Model\Reservation;
use App\Repository\ReservationRepositoryInterface;
use DateTimeInterface;

/**
 * Implémentation EN MÉMOIRE de ReservationRepositoryInterface, utilisée par
 * les tests unitaires pour ne jamais toucher MySQL.
 *
 * La recherche de chevauchement reproduit EXACTEMENT la logique de
 * EloquentReservationRepository::overlapping() :
 *   salle_id = $salleId
 *   AND statut = 'confirmée'
 *   AND date_debut < fin
 *   AND date_fin > debut
 */
final class InMemoryReservationRepository implements ReservationRepositoryInterface
{
    /** @var list<array<string, mixed>> */
    private array $reservations = [];

    /** @var list<array<string, mixed>> */
    private array $created = [];

    private int $nextId = 1;

    /**
     * Pré-remplit le dépôt avec une réservation existante (fixture).
     * $donnees doit contenir au minimum : salle_id, date_debut, date_fin.
     */
    public function seed(array $donnees): int
    {
        $reservation = $donnees + [
            'id'          => $this->nextId++,
            'statut'      => Reservation::STATUT_CONFIRMEE,
            'responsable' => 'Réserver initial',
            'email'       => 'initial@univ.fr',
            'motif'       => 'Réservation déjà existante',
        ];

        $this->reservations[] = $reservation;

        return (int) $reservation['id'];
    }

    public function create(array $donnees): Reservation
    {
        $reservation = $donnees + [
            'id'     => $this->nextId++,
            'statut' => Reservation::STATUT_CONFIRMEE,
        ];

        $this->created[] = $reservation;
        $this->reservations[] = $reservation;

        return $this->toModel($reservation);
    }

    public function findById(int $id): ?Reservation
    {
        foreach ($this->reservations as $reservation) {
            if ((int) $reservation['id'] === $id) {
                return $this->toModel($reservation);
            }
        }

        return null;
    }

    public function all(): array
    {
        return array_map(
            fn (array $reservation): Reservation => $this->toModel($reservation),
            $this->reservations
        );
    }

    public function forSalle(int $salleId): array
    {
        $rows = array_filter(
            $this->reservations,
            fn (array $reservation): bool => (int) $reservation['salle_id'] === $salleId
        );

        return array_map(
            fn (array $reservation): Reservation => $this->toModel($reservation),
            array_values($rows)
        );
    }

    public function overlapping(int $salleId, DateTimeInterface $debut, DateTimeInterface $fin): array
    {
        $debut = $debut->format('Y-m-d H:i:s');
        $fin = $fin->format('Y-m-d H:i:s');

        $rows = array_filter(
            $this->reservations,
            fn (array $reservation): bool =>
                (int) $reservation['salle_id'] === $salleId
                && $reservation['statut'] === Reservation::STATUT_CONFIRMEE
                && $reservation['date_debut'] < $fin
                && $reservation['date_fin'] > $debut
        );

        return array_map(
            fn (array $reservation): Reservation => $this->toModel($reservation),
            array_values($rows)
        );
    }

    public function cancel(int $id): bool
    {
        foreach ($this->reservations as &$reservation) {
            if ((int) $reservation['id'] === $id) {
                $reservation['statut'] = Reservation::STATUT_ANNULEE;

                return true;
            }
        }

        return false;
    }

    /** Données passées à create() (pour vérifier la persistance simulée). */
    public function createdReservations(): array
    {
        return $this->created;
    }

    /** @param array<string, mixed> $donnees */
    private function toModel(array $donnees): Reservation
    {
        // setRawAttributes() (au lieu de setAttribute) pour NE PAS déclencher
        // les casts 'datetime' du modèle Reservation, qui exigent une connexion
        // MySQL : les tests unitaires restent 100 % en mémoire.
        $reservation = new Reservation();
        $reservation->setRawAttributes([
            'id'          => (int) $donnees['id'],
            'salle_id'    => (int) $donnees['salle_id'],
            'responsable' => (string) $donnees['responsable'],
            'email'       => (string) $donnees['email'],
            'motif'       => (string) $donnees['motif'],
            'date_debut'  => (string) $donnees['date_debut'],
            'date_fin'    => (string) $donnees['date_fin'],
            'statut'      => (string) $donnees['statut'],
        ]);

        return $reservation;
    }
}