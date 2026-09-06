<?php

declare(strict_types=1);

namespace App\Validation;

use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator as v;














final class ReservationValidator implements ValidatorInterface
{
    private const MESSAGES = [
        'salle_id'    => 'La salle est obligatoire.',
        'responsable' => 'Le responsable est obligatoire (2 à 120 caractères).',
        'email'       => 'L’adresse e-mail est invalide.',
        'motif'       => 'Le motif doit contenir entre 5 et 255 caractères.',
        'date_debut'  => 'La date de début est invalide.',
        'date_fin'    => 'La date de fin est invalide.',
    ];

    public function validate(array $data): ValidationResult
    {
        $validator = v::key('salle_id', v::notEmpty()->intVal()->positive()->setName('salle_id'))
            ->key('responsable', v::notEmpty()->length(2, 120)->setName('responsable'))
            ->key('email', v::notEmpty()->email()->setName('email'))
            ->key('motif', v::notEmpty()->length(5, 255)->setName('motif'))
            ->key('date_debut', v::notEmpty()->oneOf(
                v::dateTime('Y-m-d H:i:s'),
                v::dateTime('Y-m-d\TH:i')
            )->setName('date_debut'))
            ->key('date_fin', v::notEmpty()->oneOf(
                v::dateTime('Y-m-d H:i:s'),
                v::dateTime('Y-m-d\TH:i')
            )->setName('date_fin'));

        try {
            $validator->assert($data);

            return ValidationResult::valid($data);
        } catch (NestedValidationException $e) {
            return ValidationResult::invalid($this->frenchMessages($e->getMessages()));
        }
    }

    
    private function frenchMessages(array $messages): array
    {
        $french = [];
        foreach ($messages as $field => $message) {
            $french[$field] = self::MESSAGES[$field] ?? $message;
        }

        return $french;
    }
}