<?php

declare(strict_types=1);

use App\Application;
use App\Container\ContainerFactory;

require dirname(__DIR__) . '/vendor/autoload.php';
require dirname(__DIR__) . '/config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$container = ContainerFactory::fromConfig(dirname(__DIR__) . '/config/container.php')->create();

$application = $container->get(Application::class);
$application->run();
