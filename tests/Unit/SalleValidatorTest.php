<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Validation\SalleValidator;
use PHPUnit\Framework\TestCase;

/**
 * @cahier-des-charges
 * Critère « Valider une salle » : type de salle inconnu, capacité négative,
 * et chemin heureux.
 */
final class SalleValidatorTest extends TestCase
{
    public function testUneCapaciteNegativeEstRefusee(): void
    {
        $donnees = [
            'nom'      => 'Salle B12',
            'batiment' => 'Bâtiment A',
            'capacite' => -5,
            'type'     => 'cours',
            'active'   => true,
        ];

        $resultat = (new SalleValidator())->validate($donnees);

        self::assertFalse($resultat->isValid());
        self::assertArrayHasKey('capacite', $resultat->errors());
    }

    public function testUnTypeDeSalleInconnuEstRefuse(): void
    {
        $donnees = [
            'nom'      => 'Salle B12',
            'batiment' => 'Bâtiment A',
            'capacite' => 30,
            'type'     => 'auditorium',
            'active'   => true,
        ];

        $resultat = (new SalleValidator())->validate($donnees);

        self::assertFalse($resultat->isValid());
        self::assertArrayHasKey('type', $resultat->errors());
    }

    public function testUneSalleValideEstAcceptee(): void
    {
        $donnees = [
            'nom'      => 'Salle B12',
            'batiment' => 'Bâtiment A',
            'capacite' => 30,
            'type'     => 'cours',
            'active'   => true,
        ];

        $resultat = (new SalleValidator())->validate($donnees);

        self::assertTrue($resultat->isValid());
        self::assertSame([], $resultat->errors());
    }
}