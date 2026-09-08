<?php

declare(strict_types=1);

namespace App\Database;

use Illuminate\Database\Capsule\Manager;

final class CapsuleFactory
{
    public static function create(): Manager
    {
        $capsule = new Manager();
        $capsule->addConnection(self::connectionParams());
        $capsule->setAsGlobal();
        $capsule->bootEloquent();

        return $capsule;
    }

    public static function connectionParams(): array
    {
        return [
            'driver'    => $_ENV['DB_DRIVER'] ?? 'mysql',
            'host'      => $_ENV['DB_HOST'] ?? '127.0.0.1',
            'port'      => (int) ($_ENV['DB_PORT'] ?? 3306),
            'database'  => $_ENV['DB_DATABASE'] ?? 'reservation_salles',
            'username'  => $_ENV['DB_USERNAME'] ?? 'root',
            'password'  => $_ENV['DB_PASSWORD'] ?? '',
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix'    => '',
        ];
    }
}