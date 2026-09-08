<?php

declare(strict_types=1);

use App\Application;
use DI\ContainerBuilder;

require dirname(__DIR__) . '/vendor/autoload.php';
require dirname(__DIR__) . '/config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$builder = new ContainerBuilder();
$builder->addDefinitions(dirname(__DIR__) . '/config/container.php');
$container = $builder->build();

$application = $container->get(Application::class);
$application->run();
