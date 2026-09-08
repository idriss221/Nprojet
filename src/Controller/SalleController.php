<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\CreerSalleDTO;
use App\Exception\ReservationIntrouvableException;
use App\Exception\SalleIndisponibleException;
use App\Service\SalleService;
use App\Validation\SalleValidator;

final class SalleController extends AbstractController
{
    public function __construct(
        private readonly SalleService $salles,
        private readonly SalleValidator $validateur
    ) {
    }

    public function index(): string
    {
        return $this->render('salle/index.php', [
            'salles' => $this->salles->toutes(),
        ], 'Salles');
    }

    public function show(int $id): string
    {
        $salle = $this->salles->retrouver($id);

        if ($salle === null) {
            http_response_code(404);

            return $this->render('error/404.php', [], 'Page introuvable');
        }

        return $this->render('salle/show.php', [
            'salle' => $salle,
        ], $salle->nom);
    }

    public function create(): string
    {
        return $this->render('salle/form.php', [
            'salle'     => null,
            'erreurs'   => [],
            'anciennes' => [],
        ], 'Nouvelle salle');
    }

    public function store(): never
    {
        $donnees = $_POST;
        $resultat = $this->validateur->validate($donnees);

        if (! $resultat->isValid()) {
            $this->formulaireAvecErreurs($donnees, $resultat->errors(), null);
        }

        try {
            $salle = $this->salles->creer(CreerSalleDTO::fromArray($donnees));
        } catch (SalleIndisponibleException $e) {
            $this->formulaireAvecErreurs($donnees, ['nom' => $e->getMessage()], null);
        }

        $_SESSION['flash'] = 'La salle « ' . $salle->nom . ' » a été créée.';
        $this->rediriger('/salles');
    }

    public function edit(int $id): string
    {
        $salle = $this->salles->retrouver($id);

        if ($salle === null) {
            http_response_code(404);

            return $this->render('error/404.php', [], 'Page introuvable');
        }

        return $this->render('salle/form.php', [
            'salle'     => $salle,
            'erreurs'   => [],
            'anciennes' => [],
        ], 'Modifier la salle');
    }

    public function update(int $id): never
    {
        $donnees = $_POST;
        $resultat = $this->validateur->validate($donnees);

        if (! $resultat->isValid()) {
            $this->formulaireAvecErreurs($donnees, $resultat->errors(), $id);
        }

        try {
            $this->salles->modifier($id, CreerSalleDTO::fromArray($donnees));
        } catch (ReservationIntrouvableException $e) {
            $this->formulaireAvecErreurs($donnees, ['nom' => $e->getMessage()], $id);
        }

        $_SESSION['flash'] = 'La salle a été modifiée.';
        $this->rediriger('/salles/' . $id);
    }

    private function formulaireAvecErreurs(array $anciennes, array $erreurs, ?int $id): never
    {
        echo $this->render('salle/form.php', [
            'salle'     => $id === null ? null : $this->salles->retrouver($id),
            'erreurs'   => $erreurs,
            'anciennes' => $anciennes,
        ], 'Salle');
        exit;
    }
}
