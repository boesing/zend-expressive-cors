<?php
declare(strict_types=1);

namespace Boesing\Expressive\CorsTest\Service;

use Boesing\Expressive\Cors\Configuration\ConfigurationInterface;
use Boesing\Expressive\Cors\Configuration\RouteConfigurationFactoryInterface;
use Boesing\Expressive\Cors\Service\ConfigurationLocator;
use Boesing\Expressive\Cors\Service\ConfigurationLocatorFactory;
use Boesing\Expressive\CorsTest\AbstractFactoryTest;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Zend\Expressive\Router\RouterInterface;

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
