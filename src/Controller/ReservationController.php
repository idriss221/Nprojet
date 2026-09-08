<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\CreerReservationDTO;
use App\Exception\ReservationIntrouvableException;
use App\Exception\SalleIndisponibleException;
use App\Service\AnnulerReservationService;
use App\Service\CreerReservationService;
use App\Service\SalleService;
use App\Validation\ReservationValidator;

final class ReservationController extends AbstractController
{
    public function __construct(
        private readonly CreerReservationService $creerReservation,
        private readonly AnnulerReservationService $annulerReservation,
        private readonly SalleService $salles,
        private readonly ReservationValidator $validateur
    ) {
    }

    public function index(): string
    {
        $salleId = isset($_GET['salle']) && ctype_digit($_GET['salle'])
            ? (int) $_GET['salle']
            : null;

        $reservations = $salleId === null
            ? $this->annulerReservation->toutes()
            : $this->annulerReservation->pourSalle($salleId);

        return $this->render('reservation/index.php', [
            'reservations' => $reservations,
            'salles'       => $this->salles->toutes(),
        ], 'Réservations');
    }

    public function show(int $id): string
    {
        $reservation = $this->annulerReservation->retrouver($id);

        if ($reservation === null) {
            http_response_code(404);

            return $this->render('error/404.php', [], 'Page introuvable');
        }

        return $this->render('reservation/show.php', [
            'reservation' => $reservation,
        ], 'Réservation');
    }

    public function create(): string
    {
        return $this->render('reservation/form.php', [
            'reservation' => null,
            'erreurs'     => [],
            'anciennes'   => [],
            'salles'      => $this->sallesActives(),
        ], 'Nouvelle réservation');
    }

    public function store(): never
    {
        $donnees = $_POST;
        $resultat = $this->validateur->validate($donnees);

        if (! $resultat->isValid()) {
            $this->formulaireAvecErreurs($donnees, $resultat->errors());
        }

        try {
            $reservation = $this->creerReservation->creer(CreerReservationDTO::fromArray($donnees));
        } catch (SalleIndisponibleException $e) {
            $this->formulaireAvecErreurs($donnees, ['date_debut' => $e->getMessage()]);
        }

        $_SESSION['flash'] = 'La réservation de « ' . $reservation->responsable . ' » a été enregistrée.';
        $this->rediriger('/reservations');
    }

    public function cancel(int $id): never
    {
        try {
            $this->annulerReservation->annuler($id);
            $_SESSION['flash'] = 'La réservation #' . $id . ' a été annulée.';
        } catch (ReservationIntrouvableException) {
            $_SESSION['flash'] = 'Réservation introuvable.';
        }

        $this->rediriger('/reservations');
    }

    private function formulaireAvecErreurs(array $anciennes, array $erreurs): never
    {
        echo $this->render('reservation/form.php', [
            'reservation' => null,
            'erreurs'     => $erreurs,
            'anciennes'   => $anciennes,
            'salles'      => $this->sallesActives(),
        ], 'Nouvelle réservation');
        exit;
    }

    private function sallesActives(): array
    {
        return array_values(array_filter(
            $this->salles->toutes(),
            static fn ($salle) => $salle->active
        ));
    }
}
