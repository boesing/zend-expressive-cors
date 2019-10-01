<?php
declare(strict_types=1);

namespace Boesing\Expressive\CorsTest\Middleware;

use Boesing\Expressive\Cors\Middleware\CorsMiddleware;
use Boesing\Expressive\Cors\Middleware\CorsMiddlewareFactory;
use Boesing\Expressive\Cors\Service\ConfigurationLocatorInterface;
use Boesing\Expressive\Cors\Service\CorsInterface;
use Boesing\Expressive\Cors\Service\ResponseFactoryInterface;
use Boesing\Expressive\CorsTest\AbstractFactoryTest;

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
