<?php

declare(strict_types=1);

namespace App\DTO;

use DateTimeImmutable;








final readonly class CreerReservationDTO
{
    public function __construct(
        public int $salleId,
        public string $responsable,
        public string $email,
        public string $motif,
        public DateTimeImmutable $dateDebut,
        public DateTimeImmutable $dateFin,
    ) {
    }

    


    public static function fromArray(array $data): self
    {
        return new self(
            salleId: (int) ($data['salle_id'] ?? 0),
            responsable: (string) ($data['responsable'] ?? ''),
            email: (string) ($data['email'] ?? ''),
            motif: (string) ($data['motif'] ?? ''),
            dateDebut: self::convertDate($data['date_debut'] ?? ''),
            dateFin: self::convertDate($data['date_fin'] ?? ''),
        );
    }

    


    private static function convertDate(string $date): DateTimeImmutable
    {
        foreach (['Y-m-d H:i:s', 'Y-m-d\TH:i'] as $format) {
            $parsed = DateTimeImmutable::createFromFormat($format, $date);
            if ($parsed !== false) {
                return $parsed;
            }
        }

        return new DateTimeImmutable($date);
    }
}