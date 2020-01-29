<?php

declare(strict_types=1);

namespace Boesing\Mezzio\Cors\Middleware\Exception;

use Boesing\Mezzio\Cors\Exception\ExceptionInterface;
use Boesing\Mezzio\Cors\Middleware\CorsMiddleware;
use LogicException;
use Mezzio\Router\Middleware\DispatchMiddleware;
use Mezzio\Router\Middleware\RouteMiddleware;

use function sprintf;

final class InvalidConfigurationException extends LogicException implements ExceptionInterface
{
    public static function fromInvalidPipelineConfiguration() : self
    {
        return new self(sprintf(
            'Please re-configure your pipeline. It seems that the `%s` is not between the `%s` and the `%s`',
            CorsMiddleware::class,
            RouteMiddleware::class,
            DispatchMiddleware::class
        ));
    }
}
