<?php

declare(strict_types=1);

namespace Boesing\Expressive\Cors\Service;

use Boesing\Expressive\Cors\Configuration\ConfigurationInterface;
use Boesing\Expressive\Cors\Configuration\RouteConfigurationFactoryInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Zend\Expressive\Router\RouterInterface;

final class ConfigurationLocatorFactory
{
    public function __invoke(ContainerInterface $container) : ConfigurationLocator
    {
        return new ConfigurationLocator(
            $container->get(ConfigurationInterface::class),
            $container->get(ServerRequestFactoryInterface::class),
            $container->get(RouterInterface::class),
            $container->get(RouteConfigurationFactoryInterface::class)
        );
    }
}
