<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Validation\ReservationValidator;
use PHPUnit\Framework\TestCase;

/**
 * @cahier-des-charges
 * Critère « Valider une réservation » : adresse électronique invalide,
 * responsable vide, date incorrecte, et chemin heureux.
 */
final class ReservationValidatorTest extends TestCase
{
    private const DONNEES_VALIDES = [
        'salle_id'    => 5,
        'responsable' => 'Jean Dupont',
        'email'       => 'jean.dupont@univ.fr',
        'motif'       => 'TP de mathématiques',
        'date_debut'  => '2026-10-01 10:00:00',
        'date_fin'    => '2026-10-01 12:00:00',
    ];

    public function testUneAdresseElectroniqueInvalideEstRefusee(): void
    {
        $donnees = self::DONNEES_VALIDES;
        $donnees['email'] = 'pas-une-adresse';

        $resultat = (new ReservationValidator())->validate($donnees);

        self::assertFalse($resultat->isValid());
        self::assertArrayHasKey('email', $resultat->errors());
    }

    public function testUnResponsableVideEstRefuse(): void
    {
        $donnees = self::DONNEES_VALIDES;
        $donnees['responsable'] = '';

        $resultat = (new ReservationValidator())->validate($donnees);

        self::assertFalse($resultat->isValid());
        self::assertArrayHasKey('responsable', $resultat->errors());
    }

    public function testUneDateIncorrecteEstRefusee(): void
    {
        $donnees = self::DONNEES_VALIDES;
        $donnees['date_debut'] = '2026-13-45 10:00:00';

        $resultat = (new ReservationValidator())->validate($donnees);

        self::assertFalse($resultat->isValid());
        self::assertArrayHasKey('date_debut', $resultat->errors());
    }

    public function testUneReservationValideEstAcceptee(): void
    {
        $resultat = (new ReservationValidator())->validate(self::DONNEES_VALIDES);

        self::assertTrue($resultat->isValid());
        self::assertSame([], $resultat->errors());
    }
}