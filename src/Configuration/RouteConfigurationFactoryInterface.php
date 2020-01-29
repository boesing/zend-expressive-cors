<?php

declare(strict_types=1);

namespace Boesing\Mezzio\Cors\Configuration;

interface RouteConfigurationFactoryInterface
{
    public function __invoke(array $parameters) : RouteConfigurationInterface;
}
