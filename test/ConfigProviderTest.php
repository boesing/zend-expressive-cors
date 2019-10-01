<?php

declare(strict_types=1);

namespace Boesing\Expressive\CorsTest;

use Boesing\Expressive\Cors\ConfigProvider;
use Boesing\Expressive\Cors\Configuration\ConfigurationInterface;
use Boesing\Expressive\Cors\Configuration\ProjectConfiguration;
use Boesing\Expressive\Cors\Configuration\ProjectConfigurationFactory;
use Boesing\Expressive\Cors\Configuration\RouteConfigurationFactory;
use Boesing\Expressive\Cors\Configuration\RouteConfigurationFactoryFactory;
use Boesing\Expressive\Cors\Configuration\RouteConfigurationFactoryInterface;
use Boesing\Expressive\Cors\Middleware\CorsMiddleware;
use Boesing\Expressive\Cors\Middleware\CorsMiddlewareFactory;
use Boesing\Expressive\Cors\Service\ConfigurationLocator;
use Boesing\Expressive\Cors\Service\ConfigurationLocatorFactory;
use Boesing\Expressive\Cors\Service\ConfigurationLocatorInterface;
use Boesing\Expressive\Cors\Service\Cors;
use Boesing\Expressive\Cors\Service\CorsFactory;
use Boesing\Expressive\Cors\Service\CorsInterface;
use Boesing\Expressive\Cors\Service\ResponseFactory;
use Boesing\Expressive\Cors\Service\ResponseFactoryFactory;
use Boesing\Expressive\Cors\Service\ResponseFactoryInterface;
use PHPUnit\Framework\TestCase;

final class ConfigProviderTest extends TestCase
{
    public function testWillProvideExpectedConfiguration() : void
    {
        $provider = new ConfigProvider();
        $config   = $provider();

        $this->assertSame([
            'dependencies' => $provider->getServiceDependencies(),
        ], $config);
    }

    public function testWillProvideExpectedServices() : void
    {
        $provider = new ConfigProvider();
        $services = $provider->getServiceDependencies();

        $this->assertSame([
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
        ], $services);
    }
}
