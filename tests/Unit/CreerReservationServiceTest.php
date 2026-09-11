<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\DTO\CreerReservationDTO;
use App\DTO\CreerReservationDTOBuilder;
use App\Exception\SalleIndisponibleException;
use App\Model\Reservation;
use App\Model\Salle;
use App\Service\CreerReservationService;
use App\Tests\Unit\Fakes\InMemoryReservationRepository;
use App\Tests\Unit\Fakes\InMemorySalleRepository;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

/**
 * @cahier-des-charges
 * Critère « Créer une réservation » : toutes les situations imposées par le
 * sujet (succès + 6 refus + cas limite « sans chevauchement »).
 *
 * Aucun de ces tests ne touche MySQL : les deux repositories sont remplacés
 * par des fakes en mémoire (tests/Unit/Fakes).
 */
final class CreerReservationServiceTest extends TestCase
{
    private InMemorySalleRepository $salles;

    private InMemoryReservationRepository $reservations;

    protected function setUp(): void
    {
        parent::setUp();

        $this->salles = new InMemorySalleRepository();
        $this->reservations = new InMemoryReservationRepository();
    }

    public function testUneReservationValideEstCreee(): void
    {
        // Situation 1 : réservation valide → succès (la réservation est créée et retournée).
        $this->seedSalle();

        $debut = new DateTimeImmutable('+1 hour');
        $fin = new DateTimeImmutable('+3 hours');
        $dto = $this->dto(1, $debut, $fin);

        $reservation = $this->service()->creer($dto);

        self::assertInstanceOf(Reservation::class, $reservation);
        self::assertSame(1, $reservation->salle_id);
        self::assertSame('Jean Dupont', $reservation->responsable);
        self::assertSame('jean.dupont@univ.fr', $reservation->email);
        self::assertSame('TP de mathématiques', $reservation->motif);
        self::assertSame(Reservation::STATUT_CONFIRMEE, $reservation->statut);

        // Le dépôt fake a bien reçu UNE création avec les données attendues.
        $creees = $this->reservations->createdReservations();
        self::assertCount(1, $creees);
        self::assertSame(1, $creees[0]['salle_id']);
        self::assertSame('Jean Dupont', $creees[0]['responsable']);
        self::assertSame('jean.dupont@univ.fr', $creees[0]['email']);
        self::assertSame('TP de mathématiques', $creees[0]['motif']);
        self::assertSame($debut->format('Y-m-d H:i:s'), $creees[0]['date_debut']);
        self::assertSame($fin->format('Y-m-d H:i:s'), $creees[0]['date_fin']);
    }

    public function testSalleInexistanteRefusee(): void
    {
        // Situation 2 : salle inexistante → SalleIndisponibleException.
        // Aucune salle n'est pré-remplie dans le dépôt.
        $this->expectSalleIndisponible(
            'La salle demandée n’existe pas.',
            fn () => $this->service()->creer($this->dto(999)),
        );
    }

    public function testSalleInactiveRefusee(): void
    {
        // Situation 3 : salle inactive → SalleIndisponibleException.
        $this->seedSalle(active: false, nom: 'Salle B12');

        $message = 'La salle « Salle B12 » est inactive : réservation impossible.';
        $this->expectSalleIndisponible(
            $message,
            fn () => $this->service()->creer($this->dto(1)),
        );
    }

    public function testDateDeFinAntérieureAuDebutRefusee(): void
    {
        // Situation 4 : date de fin antérieure à la date de début → refus.
        $this->seedSalle();

        $debut = new DateTimeImmutable('+1 hour');
        $fin = new DateTimeImmutable('-1 hour');
        $this->expectSalleIndisponible(
            'La date de fin doit être après la date de début.',
            fn () => $this->service()->creer($this->dto(1, $debut, $fin)),
        );
    }

    public function testDureeSuperieureAQuatreHeuresRefusee(): void
    {
        // Situation 5 : durée > 4 heures (règle : durée > 4 * 60 minutes) → refus.
        // 5 heures entre début et fin.
        $this->seedSalle();

        $debut = new DateTimeImmutable('+1 hour');
        $fin = new DateTimeImmutable('+6 hours');
        $this->expectSalleIndisponible(
            'Une réservation ne peut pas dépasser 4 heures.',
            fn () => $this->service()->creer($this->dto(1, $debut, $fin)),
        );
    }

    public function testDatePasseeRefusee(): void
    {
        // Situation 6 : date passée → refus.
        $this->seedSalle();

        $debut = new DateTimeImmutable('-1 hour');
        $fin = new DateTimeImmutable('+1 hour');
        $this->expectSalleIndisponible(
            'La date de début doit être dans le futur.',
            fn () => $this->service()->creer($this->dto(1, $debut, $fin)),
        );
    }

    public function testConflitAvecReservationExistanteRefuse(): void
    {
        // Situation 7 : chevauchement avec une réservation existante → refus.
        $this->seedSalle();
        // Réservation existante : +1h → +3h. Nouvelle demande : +2h → +4h (chevauchement).
        $this->seedReservation(new DateTimeImmutable('+1 hour'), new DateTimeImmutable('+3 hours'));

        $debut = new DateTimeImmutable('+2 hours');
        $fin = new DateTimeImmutable('+4 hours');

        $this->expectSalleIndisponible(
            'Cette salle est déjà réservée sur la période demandée.',
            fn () => $this->service()->creer($this->dto(1, $debut, $fin)),
        );

        // La création n'a pas dû être persistée : aucune écriture dans le dépôt.
        self::assertSame([], $this->reservations->createdReservations());
    }

    public function testReservationVoisineSansChevauchementAcceptee(): void
    {
        // Situation 8 : réservation voisine SANS chevauchement (existant 10h→12h,
        // nouvelle 12h→14h, c'est-à-dire 2h et 4h relatives) → succès.
        $this->seedSalle();
        // Réservation existante : +1h → +3h.
        $this->seedReservation(new DateTimeImmutable('+1 hour'), new DateTimeImmutable('+3 hours'));

        // Nouvelle demande : +3h → +5h : fin de l'ancienne = début de la nouvelle.
        $debut = new DateTimeImmutable('+3 hours');
        $fin = new DateTimeImmutable('+5 hours');

        $reservation = $this->service()->creer($this->dto(1, $debut, $fin));

        self::assertInstanceOf(Reservation::class, $reservation);
        $creees = $this->reservations->createdReservations();
        self::assertCount(1, $creees);
        self::assertSame($debut->format('Y-m-d H:i:s'), $creees[0]['date_debut']);
        self::assertSame($fin->format('Y-m-d H:i:s'), $creees[0]['date_fin']);
    }

    private function service(): CreerReservationService
    {
        return new CreerReservationService($this->salles, $this->reservations);
    }

    private function seedSalle(int $id = 1, bool $active = true, string $nom = 'Salle B12'): Salle
    {
        $salle = new Salle();
        $salle->id = $id;
        $salle->nom = $nom;
        $salle->active = $active;

        $this->salles->add($salle);

        return $salle;
    }

    private function seedReservation(DateTimeImmutable $debut, DateTimeImmutable $fin, int $salleId = 1): void
    {
        $this->reservations->seed([
            'salle_id'    => $salleId,
            'date_debut'  => $debut->format('Y-m-d H:i:s'),
            'date_fin'    => $fin->format('Y-m-d H:i:s'),
        ]);
    }

    private function dto(
        int $salleId = 1,
        ?DateTimeImmutable $debut = null,
        ?DateTimeImmutable $fin = null
    ): CreerReservationDTO {
        return (new CreerReservationDTOBuilder())
            ->salle($salleId)
            ->responsable('Jean Dupont')
            ->email('jean.dupont@univ.fr')
            ->motif('TP de mathématiques')
            ->debut($debut ?? new DateTimeImmutable('+1 hour'))
            ->fin($fin ?? new DateTimeImmutable('+3 hours'))
            ->build();
    }

    /**
     * Exécute une action dont on attend une SalleIndisponibleException,
     * vérifie le message exact PUIS qu'aucune écriture n'a été persistée.
     *
     * @param callable(): void $action
     */
    private function expectSalleIndisponible(string $message, callable $action): void
    {
        try {
            $action();
            self::fail('Une SalleIndisponibleException aurait dû être levée.');
        } catch (SalleIndisponibleException $exception) {
            self::assertSame($message, $exception->getMessage());
        }

        self::assertSame([], $this->reservations->createdReservations());
    }
}