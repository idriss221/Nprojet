<?php

declare(strict_types=1);

use App\Application;
use App\Controller\ReservationController;
use App\Controller\SalleController;
use App\Repository\EloquentReservationRepository;
use App\Repository\EloquentSalleRepository;
use App\Repository\ReservationRepositoryInterface;
use App\Repository\SalleRepositoryInterface;
use App\Service\AnnulerReservationService;
use App\Service\CreerReservationService;
use App\Service\SalleService;
use App\Validation\ReservationValidator;
use App\Validation\SalleValidator;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use Illuminate\Database\Capsule\Manager;
use function DI\autowire;
use function DI\factory;

return [
    SalleRepositoryInterface::class => autowire(EloquentSalleRepository::class),
    ReservationRepositoryInterface::class => autowire(EloquentReservationRepository::class),

    Manager::class => factory(static function (): Manager {
        $capsule = new Manager();
        $capsule->addConnection([
            'driver'    => $_ENV['DB_DRIVER'] ?? 'mysql',
            'host'      => $_ENV['DB_HOST'] ?? '127.0.0.1',
            'port'      => (int) ($_ENV['DB_PORT'] ?? 3306),
            'database'  => $_ENV['DB_DATABASE'] ?? 'reservation_salles',
            'username'  => $_ENV['DB_USERNAME'] ?? 'root',
            'password'  => $_ENV['DB_PASSWORD'] ?? '',
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix'    => '',
        ]);
        $capsule->setAsGlobal();
        $capsule->bootEloquent();

        return $capsule;
    }),

    Dispatcher::class => factory(static function (): Dispatcher {
        return \FastRoute\simpleDispatcher(require dirname(__DIR__) . '/routes/web.php');
    }),
];
