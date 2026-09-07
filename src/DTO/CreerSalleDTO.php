<?php

declare(strict_types=1);

namespace App\DTO;








final readonly class CreerSalleDTO
{
    public function __construct(
        public string $nom,
        public string $batiment,
        public int $capacite,
        public string $type,
        public bool $active,
    ) {
    }

    


    public static function fromArray(array $data): self
    {
        return new self(
            nom: (string) ($data['nom'] ?? ''),
            batiment: (string) ($data['batiment'] ?? ''),
            capacite: (int) ($data['capacite'] ?? 0),
            type: (string) ($data['type'] ?? ''),
            active: filter_var($data['active'] ?? false, FILTER_VALIDATE_BOOLEAN),
        );
    }
}