<?php
declare(strict_types=1);

namespace Boesing\Mezzio\CorsTest\Service;

use Boesing\Mezzio\Cors\Service\ResponseFactory;
use Boesing\Mezzio\Cors\Service\ResponseFactoryFactory;
use Boesing\Mezzio\CorsTest\AbstractFactoryTest;
use Psr\Http\Message\ResponseFactoryInterface;

final class ResponseFactoryFactoryTest extends AbstractFactoryTest
{

    /**
     * @return array<string,string|array|object>
     */
    protected function dependencies(): array
    {
        return [
            ResponseFactoryInterface::class => ResponseFactoryInterface::class,
        ];
    }

    protected function factory(): callable
    {
        return new ResponseFactoryFactory();
    }

    /**
     * Implement this for post creation assertions.
     */
    protected function postCreationAssertions($instance): void
    {
        $this->assertInstanceOf(ResponseFactory::class, $instance);
    }
}
