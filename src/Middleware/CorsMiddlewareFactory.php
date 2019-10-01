<?php

declare(strict_types=1);

namespace Boesing\Expressive\Cors\Middleware;

use Boesing\Expressive\Cors\Service\ConfigurationLocatorInterface;
use Boesing\Expressive\Cors\Service\CorsInterface;
use Boesing\Expressive\Cors\Service\ResponseFactoryInterface;
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
