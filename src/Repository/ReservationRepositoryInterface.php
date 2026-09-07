<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\Reservation;
use DateTimeInterface;








interface ReservationRepositoryInterface
{
    
    public function create(array $donnees): Reservation;

    
    public function findById(int $id): ?Reservation;

    
    public function all(): array;

    
    public function forSalle(int $salleId): array;

    



    public function overlapping(int $salleId, DateTimeInterface $debut, DateTimeInterface $fin): array;

    
    public function cancel(int $id): bool;
}