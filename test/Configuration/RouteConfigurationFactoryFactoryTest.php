<?php
declare(strict_types=1);

namespace Boesing\Expressive\CorsTest\Configuration;

use Boesing\Expressive\Cors\Configuration\RouteConfigurationFactory;
use Boesing\Expressive\Cors\Configuration\RouteConfigurationFactoryFactory;
use Boesing\Expressive\CorsTest\AbstractFactoryTest;

final class RouteConfigurationFactoryFactoryTest extends AbstractFactoryTest
{

    /**
     * @return array<string,string|array|object>
     */
    protected function dependencies(): array
    {
        return [];
    }

    protected function factory(): callable
    {
        return new RouteConfigurationFactoryFactory();
    }

    /**
     * Implement this for post creation assertions.
     */
    protected function postCreationAssertions($instance): void
    {
        $this->assertInstanceOf(RouteConfigurationFactory::class, $instance);
    }
}
