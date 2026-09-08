<?php

declare(strict_types=1);

namespace App\Container;

use DI\Container;
use DI\ContainerBuilder;

final class ContainerFactory
{
    public function __construct(private readonly string $configPath)
    {
    }

    public static function fromConfig(string $configPath): self
    {
        return new self($configPath);
    }

    public function create(): Container
    {
        $builder = new ContainerBuilder();
        $builder->addDefinitions($this->configPath);

        return $builder->build();
    }
}
