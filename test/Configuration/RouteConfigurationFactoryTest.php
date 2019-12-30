<?php
declare(strict_types=1);

namespace Boesing\Expressive\CorsTest\Configuration;

use Boesing\Expressive\Cors\Configuration\RouteConfigurationFactory;
use Boesing\Expressive\Cors\Configuration\RouteConfigurationInterface;
use PHPUnit\Framework\TestCase;

final class RouteConfigurationFactoryTest extends TestCase
{

    /**
     * @var RouteConfigurationFactory
     */
    private $factory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->factory = new RouteConfigurationFactory();
    }

    public function testWillInstantiateRouteConfiguration(): void
    {
        $factory = $this->factory;
        $instance = $factory([]);
        $this->assertInstanceOf(RouteConfigurationInterface::class, $instance);
    }
}
