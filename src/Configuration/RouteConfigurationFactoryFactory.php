<?php

declare(strict_types=1);

namespace Boesing\Expressive\Cors\Configuration;

use Psr\Container\ContainerInterface;

final class RouteConfigurationFactoryFactory
{
    public function __invoke(ContainerInterface $container) : RouteConfigurationFactory
    {
        return new RouteConfigurationFactory();
    }
}
