<?php

declare(strict_types=1);

namespace Boesing\Expressive\Cors\Configuration;

final class RouteConfigurationFactory implements RouteConfigurationFactoryInterface
{
    public function __invoke(array $parameters) : RouteConfigurationInterface
    {
        return new RouteConfiguration($parameters);
    }
}
