<?php

declare(strict_types=1);

namespace Boesing\Mezzio\Cors\Middleware;

use Boesing\Mezzio\Cors\Service\ConfigurationLocatorInterface;
use Boesing\Mezzio\Cors\Service\CorsInterface;
use Boesing\Mezzio\Cors\Service\ResponseFactoryInterface;
use Psr\Container\ContainerInterface;

final class CorsMiddlewareFactory
{
    public function __invoke(ContainerInterface $container) : CorsMiddleware
    {
        return new CorsMiddleware(
            $container->get(CorsInterface::class),
            $container->get(ConfigurationLocatorInterface::class),
            $container->get(ResponseFactoryInterface::class)
        );
    }
}
