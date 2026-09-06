<?php

declare(strict_types=1);

namespace App\Validation;

use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator as v;










final class SalleValidator implements ValidatorInterface
{
    public const TYPES = [
        'cours',
        'informatique',
        'laboratoire',
        'amphitheatre',
        'reunion',
    ];

    private const MESSAGES = [
        'nom'      => 'Le nom est obligatoire (2 à 100 caractères).',
        'batiment' => 'Le bâtiment est obligatoire (2 à 100 caractères).',
        'capacite' => 'La capacité doit être un entier compris entre 1 et 1 000.',
        'type'     => 'Le type de salle est invalide (cours, informatique, laboratoire, amphitheatre, reunion).',
        'active'   => 'Le champ « active » doit être un booléen.',
    ];

    public function validate(array $data): ValidationResult
    {
        $validator = v::key('nom', v::notEmpty()->length(2, 100)->setName('nom'))
            ->key('batiment', v::notEmpty()->length(2, 100)->setName('batiment'))
            ->key('capacite', v::notEmpty()->intVal()->between(1, 1000)->setName('capacite'))
            ->key('type', v::notEmpty()->in(self::TYPES)->setName('type'))
            ->key('active', v::boolVal()->setName('active'), false);

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