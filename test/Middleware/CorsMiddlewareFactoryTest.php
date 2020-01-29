<?php
declare(strict_types=1);

namespace Boesing\Mezzio\CorsTest\Middleware;

use Boesing\Mezzio\Cors\Middleware\CorsMiddleware;
use Boesing\Mezzio\Cors\Middleware\CorsMiddlewareFactory;
use Boesing\Mezzio\Cors\Service\ConfigurationLocatorInterface;
use Boesing\Mezzio\Cors\Service\CorsInterface;
use Boesing\Mezzio\Cors\Service\ResponseFactoryInterface;
use Boesing\Mezzio\CorsTest\AbstractFactoryTest;

final class CorsMiddlewareFactoryTest extends AbstractFactoryTest
{

    /**
     * @return string[]
     */
    protected function dependencies(): array
    {
        return [
            CorsInterface::class => CorsInterface::class,
            ConfigurationLocatorInterface::class => ConfigurationLocatorInterface::class,
            ResponseFactoryInterface::class => ResponseFactoryInterface::class,
        ];
    }

    protected function factory(): callable
    {
        return new CorsMiddlewareFactory();
    }

    protected function postCreationAssertions($instance): void
    {
        $this->assertInstanceOf(CorsMiddleware::class, $instance);
    }
}
