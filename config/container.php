<?php

declare(strict_types=1);

use App\Application;
use App\Controller\ReservationController;
use App\Controller\SalleController;
use App\Database\CapsuleFactory;
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

    SalleValidator::class => autowire(SalleValidator::class),
    ReservationValidator::class => autowire(ReservationValidator::class),

    SalleService::class => autowire(SalleService::class),
    CreerReservationService::class => autowire(CreerReservationService::class),
    AnnulerReservationService::class => autowire(AnnulerReservationService::class),

    SalleController::class => autowire(SalleController::class),
    ReservationController::class => autowire(ReservationController::class),

    Application::class => autowire(Application::class),

    Manager::class => factory(static function (): Manager {
        return CapsuleFactory::create();
    }),

    Dispatcher::class => factory(static function (): Dispatcher {
        return \FastRoute\simpleDispatcher(require dirname(__DIR__) . '/routes/web.php');
    }),
];
