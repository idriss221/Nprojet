<?php

declare(strict_types=1);

namespace App\Controller;

abstract class AbstractController
{
    protected string $titre = '';

    protected function render(string $vue, array $donnees = [], string $titre = ''): string
    {
        extract($donnees, EXTR_SKIP);

        $this->titre = $titre;

        ob_start();
        require dirname(__DIR__, 2) . '/templates/' . $vue;
        $contenu = (string) ob_get_clean();

        ob_start();
        require dirname(__DIR__, 2) . '/templates/layout/base.php';

        return (string) ob_get_clean();
    }

    protected function e(mixed $valeur): string
    {
        return htmlspecialchars((string) $valeur, ENT_QUOTES, 'UTF-8');
    }

    protected function rediriger(string $chemin): never
    {
        header('Location: ' . $chemin);

        exit;
    }
}
