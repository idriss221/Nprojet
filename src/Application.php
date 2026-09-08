<?php

declare(strict_types=1);

namespace App;

use DI\Container;
use FastRoute\Dispatcher;
use LogicException;

final class Application
{
    public function __construct(
        private readonly Container $conteneur,
        private readonly Dispatcher $dispatcher
    ) {
    }

    private string $titre = '';

    public function run(): void
    {
        $httpMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';

        $routeInfo = $this->dispatcher->dispatch($httpMethod, $uri);

        switch ($routeInfo[0]) {
            case Dispatcher::NOT_FOUND:
                http_response_code(404);
                echo $this->renduErreur(404);
                break;

            case Dispatcher::METHOD_NOT_ALLOWED:
                http_response_code(405);
                header('Allow: ' . implode(', ', $routeInfo[1]));
                echo $this->renduErreur(405);
                break;

            case Dispatcher::FOUND:
                [$classe, $methode] = $routeInfo[1];

                if (! is_string($classe) || ! is_string($methode)) {
                    throw new LogicException('Le handler doit être un tableau [classe, méthode].');
                }

                $variables = array_map(
                    static fn (string $valeur): int|string => ctype_digit($valeur) ? (int) $valeur : $valeur,
                    $routeInfo[2]
                );

                $resultat = $this->conteneur->call([$classe, $methode], $variables);

                if (is_string($resultat)) {
                    echo $resultat;
                }
                break;
        }
    }

    private function renduErreur(int $code): string
    {
        $this->titre = 'Erreur ' . $code;

        ob_start();
        require dirname(__DIR__) . '/templates/error/' . $code . '.php';
        $contenu = (string) ob_get_clean();

        ob_start();
        require dirname(__DIR__) . '/templates/layout/base.php';

        return (string) ob_get_clean();
    }
}
