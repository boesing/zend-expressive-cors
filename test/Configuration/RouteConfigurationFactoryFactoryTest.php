<?php
declare(strict_types=1);

namespace Boesing\Mezzio\CorsTest\Configuration;

use Boesing\Mezzio\Cors\Configuration\RouteConfigurationFactory;
use Boesing\Mezzio\Cors\Configuration\RouteConfigurationFactoryFactory;
use Boesing\Mezzio\CorsTest\AbstractFactoryTest;

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
