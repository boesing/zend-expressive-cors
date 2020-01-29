<?php

declare(strict_types=1);

namespace Boesing\Mezzio\Cors;

use Boesing\Mezzio\Cors\Configuration\ConfigurationInterface;
use Boesing\Mezzio\Cors\Configuration\ProjectConfiguration;
use Boesing\Mezzio\Cors\Configuration\ProjectConfigurationFactory;
use Boesing\Mezzio\Cors\Configuration\RouteConfigurationFactory;
use Boesing\Mezzio\Cors\Configuration\RouteConfigurationFactoryFactory;
use Boesing\Mezzio\Cors\Configuration\RouteConfigurationFactoryInterface;
use Boesing\Mezzio\Cors\Middleware\CorsMiddleware;
use Boesing\Mezzio\Cors\Middleware\CorsMiddlewareFactory;
use Boesing\Mezzio\Cors\Service\ConfigurationLocator;
use Boesing\Mezzio\Cors\Service\ConfigurationLocatorFactory;
use Boesing\Mezzio\Cors\Service\ConfigurationLocatorInterface;
use Boesing\Mezzio\Cors\Service\Cors;
use Boesing\Mezzio\Cors\Service\CorsFactory;
use Boesing\Mezzio\Cors\Service\CorsInterface;
use Boesing\Mezzio\Cors\Service\ResponseFactory;
use Boesing\Mezzio\Cors\Service\ResponseFactoryFactory;
use Boesing\Mezzio\Cors\Service\ResponseFactoryInterface;

final class ConfigProvider
{
    public function __invoke() : array
    {
        return [
            'dependencies' => $this->getServiceDependencies(),
        ];
    }

    public function getServiceDependencies() : array
    {
        return [
            'factories' => [
                ProjectConfiguration::class      => ProjectConfigurationFactory::class,
                CorsMiddleware::class            => CorsMiddlewareFactory::class,
                ConfigurationLocator::class      => ConfigurationLocatorFactory::class,
                Cors::class                      => CorsFactory::class,
                ResponseFactory::class           => ResponseFactoryFactory::class,
                RouteConfigurationFactory::class => RouteConfigurationFactoryFactory::class,
            ],
            'aliases'   => [
                ConfigurationLocatorInterface::class      => ConfigurationLocator::class,
                ConfigurationInterface::class             => ProjectConfiguration::class,
                CorsInterface::class                      => Cors::class,
                ResponseFactoryInterface::class           => ResponseFactory::class,
                RouteConfigurationFactoryInterface::class => RouteConfigurationFactory::class,
            ],
        ];
    }
}
