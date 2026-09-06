<?php

declare(strict_types=1);

namespace App\Validation;











final class ValidationResult
{
    
    private function __construct(
        private readonly bool $valid,
        private readonly array $errors,
        private readonly array $data,
    ) {
    }

    public static function valid(array $data): self
    {
        return new self(true, [], $data);
    }

    
    public static function invalid(array $errors): self
    {
        return new self(false, $errors, []);
    }

    public function isValid(): bool
    {
        return $this->valid;
    }

    
    public function errors(): array
    {
        return $this->errors;
    }

    
    public function data(): array
    {
        return $this->data;
    }
}