<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use App\Model\Reservation;
use App\Model\Salle;
use App\Repository\EloquentReservationRepository;
use DateTimeImmutable;
use Illuminate\Database\Capsule\Manager as Capsule;
use PHPUnit\Framework\TestCase;

/**
 * @cahier-des-charges
 * Critère « Persistance Eloquent » : création d'une salle, relation
 * salle/réservations, recherche de chevauchement et annulation.
 *
 * Chaque test écrit dans la base dev `reservation_salles` à l'intérieur d'une
 * TRANSACTION qui est ROLLBACK-ée en tearDown : la base n'est jamais polluée.
 */
final class EloquentReservationIntegrationTest extends TestCase
{
    private static bool $capsuleBooted = false;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        // Boute le même Eloquent/Capsule que l'application (config/database.php).
        if (! self::$capsuleBooted) {
            require_once dirname(__DIR__, 2) . '/config/database.php';
            Capsule::connection()->getPdo(); // force la connexion, échoue tôt si base injoignable
            self::$capsuleBooted = true;
        }
    }

    protected function tearDown(): void
    {
        $connection = Capsule::connection();
        if ($connection->transactionLevel() > 0) {
            $connection->rollBack();
        }

        parent::tearDown();
    }

    public function testUneSalleEstCreeeAvecEloquent(): void
    {
        // Critère « création d'une salle ».
        $this->beginTransaction();

        $nom = 'Salle IT ' . uniqid();

        $salle = Salle::create([
            'nom'      => $nom,
            'batiment' => 'Bâtiment informatique',
            'capacite' => 30,
            'type'     => 'informatique',
            'active'   => true,
        ]);

        self::assertTrue($salle->exists);
        self::assertNotNull($salle->id);
        self::assertSame($nom, $salle->nom);
        self::assertSame('informatique', $salle->type);
        self::assertTrue($salle->active);

        $recuperee = Salle::find($salle->id);
        self::assertNotNull($recuperee);
        self::assertSame($nom, $recuperee->nom);
    }

    public function testRelationSalleEtSesReservations(): void
    {
        // Critère « relation salle/réservations ».
        $this->beginTransaction();

        $salle = $this->creerSalle('Salle Relation ' . uniqid());

        $this->creerReservation((int) $salle->id, '+2 days 10:00:00', '+2 days 12:00:00');
        $this->creerReservation((int) $salle->id, '+2 days 14:00:00', '+2 days 16:00:00');

        $reservations = $salle->reservations()->get();

        self::assertCount(2, $reservations);
        foreach ($reservations as $reservation) {
            self::assertInstanceOf(Reservation::class, $reservation);
            self::assertSame((int) $salle->id, (int) $reservation->salle_id);
            self::assertSame(Reservation::STATUT_CONFIRMEE, $reservation->statut);
        }
    }

    public function testRechercheDeChevauchement(): void
    {
        // Critère « recherche de chevauchement ».
        $this->beginTransaction();

        $salle = $this->creerSalle('Salle Chevauchement ' . uniqid());

        // Réservation existante : 10h → 12h (+2 jours).
        $reservation = $this->creerReservation(
            (int) $salle->id,
            '+2 days 10:00:00',
            '+2 days 12:00:00'
        );

        $repository = new EloquentReservationRepository();

        // Fenêtre 11h → 13h : chevauche la réservation existante.
        $chevauchement = $repository->overlapping(
            (int) $salle->id,
            new DateTimeImmutable('+2 days 11:00:00'),
            new DateTimeImmutable('+2 days 13:00:00')
        );
        self::assertCount(1, $chevauchement);
        self::assertSame((int) $reservation->id, (int) $chevauchement[0]->id);

        // Fenêtre 12h → 14h : voisine SANS chevauchement → aucun résultat.
        $adjacent = $repository->overlapping(
            (int) $salle->id,
            new DateTimeImmutable('+2 days 12:00:00'),
            new DateTimeImmutable('+2 days 14:00:00')
        );
        self::assertSame([], $adjacent);

        // Une fenêtre sur une AUTRE salle ne remonte rien.
        $autreSalle = $this->creerSalle('Salle Autre ' . uniqid());
        self::assertSame(
            [],
            $repository->overlapping(
                (int) $autreSalle->id,
                new DateTimeImmutable('+2 days 11:00:00'),
                new DateTimeImmutable('+2 days 13:00:00')
            )
        );

        // Une réservation ANNULÉE ne doit plus être remontée par la recherche
        // de chevauchement (filtre statut = 'confirmée').
        $repository->cancel((int) $reservation->id);
        self::assertSame(
            [],
            $repository->overlapping(
                (int) $salle->id,
                new DateTimeImmutable('+2 days 11:00:00'),
                new DateTimeImmutable('+2 days 13:00:00')
            )
        );
    }

    public function testAnnulerUneReservation(): void
    {
        // Critère « annulation d'une réservation ».
        $this->beginTransaction();

        $salle = $this->creerSalle('Salle Annulation ' . uniqid());
        $reservation = $this->creerReservation(
            (int) $salle->id,
            '+2 days 10:00:00',
            '+2 days 12:00:00'
        );

        self::assertSame(Reservation::STATUT_CONFIRMEE, $reservation->statut);

        $repository = new EloquentReservationRepository();

        self::assertTrue($repository->cancel((int) $reservation->id));

        $miseAJour = Reservation::find($reservation->id);
        self::assertNotNull($miseAJour);
        self::assertSame(Reservation::STATUT_ANNULEE, $miseAJour->statut);

        // Annuler une réservation inexistante → false.
        self::assertFalse($repository->cancel(1_000_000_000));
    }

    private function beginTransaction(): void
    {
        Capsule::connection()->beginTransaction();
    }

    private function creerSalle(string $nom): Salle
    {
        return Salle::create([
            'nom'      => $nom,
            'batiment' => 'Bâtiment A',
            'capacite' => 30,
            'type'     => 'cours',
            'active'   => true,
        ]);
    }

    private function creerReservation(int $salleId, string $debut, string $fin): Reservation
    {
        return Reservation::create([
            'salle_id'    => $salleId,
            'responsable' => 'Jean Dupont',
            'email'       => 'jean.dupont@univ.fr',
            'motif'       => 'TP de mathématiques',
            'date_debut'  => (new DateTimeImmutable($debut))->format('Y-m-d H:i:s'),
            'date_fin'    => (new DateTimeImmutable($fin))->format('Y-m-d H:i:s'),
        ]);
    }
}