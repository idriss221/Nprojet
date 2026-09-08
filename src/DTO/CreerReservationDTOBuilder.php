<?php

declare(strict_types=1);

namespace App\DTO;

use DateTimeImmutable;

final class CreerReservationDTOBuilder
{
    private int $salleId = 0;
    private string $responsable = '';
    private string $email = '';
    private string $motif = '';
    private ?DateTimeImmutable $dateDebut = null;
    private ?DateTimeImmutable $dateFin = null;

    public function salle(int $salleId): self
    {
        $this->salleId = $salleId;

        return $this;
    }

    public function responsable(string $responsable): self
    {
        $this->responsable = $responsable;

        return $this;
    }

    public function email(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function motif(string $motif): self
    {
        $this->motif = $motif;

        return $this;
    }

    public function debut(DateTimeImmutable $dateDebut): self
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    public function fin(DateTimeImmutable $dateFin): self
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    public function build(): CreerReservationDTO
    {
        $debut = $this->dateDebut ?? new DateTimeImmutable();
        $fin = $this->dateFin ?? $debut->modify('+1 hour');

        return new CreerReservationDTO(
            salleId: $this->salleId,
            responsable: $this->responsable,
            email: $this->email,
            motif: $this->motif,
            dateDebut: $debut,
            dateFin: $fin,
        );
    }

    public static function fromArray(array $data): CreerReservationDTO
    {
        return (new self())
            ->salle((int) ($data['salle_id'] ?? 0))
            ->responsable((string) ($data['responsable'] ?? ''))
            ->email((string) ($data['email'] ?? ''))
            ->motif((string) ($data['motif'] ?? ''))
            ->debut(self::convertDate((string) ($data['date_debut'] ?? '')))
            ->fin(self::convertDate((string) ($data['date_fin'] ?? '')))
            ->build();
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
