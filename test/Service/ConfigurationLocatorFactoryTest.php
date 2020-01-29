<?php
declare(strict_types=1);

namespace Boesing\Mezzio\CorsTest\Service;

use Boesing\Mezzio\Cors\Configuration\ConfigurationInterface;
use Boesing\Mezzio\Cors\Configuration\RouteConfigurationFactoryInterface;
use Boesing\Mezzio\Cors\Service\ConfigurationLocator;
use Boesing\Mezzio\Cors\Service\ConfigurationLocatorFactory;
use Boesing\Mezzio\CorsTest\AbstractFactoryTest;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Mezzio\Router\RouterInterface;

final class ConfigurationLocatorFactoryTest extends AbstractFactoryTest
{

    /**
     * @return array<string,string|array|object>
     */
    protected function dependencies(): array
    {
        return [
            ConfigurationInterface::class => ConfigurationInterface::class,
            ServerRequestFactoryInterface::class => ServerRequestFactoryInterface::class,
            RouterInterface::class => RouterInterface::class,
            RouteConfigurationFactoryInterface::class => RouteConfigurationFactoryInterface::class,
        ];
    }

    protected function factory(): callable
    {
        return new ConfigurationLocatorFactory();
    }

    /**
     * Implement this for post creation assertions.
     */
    protected function postCreationAssertions($instance): void
    {
        $this->assertInstanceOf(ConfigurationLocator::class, $instance);
    }
}
