<?php

declare(strict_types=1);

use App\Database\CapsuleFactory;

require_once dirname(__DIR__) . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->safeLoad();

$capsule = CapsuleFactory::create();

return $capsule;