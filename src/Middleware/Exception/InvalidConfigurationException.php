<?php

declare(strict_types=1);

namespace Boesing\Expressive\Cors\Middleware\Exception;

use Boesing\Expressive\Cors\Exception\ExceptionInterface;
use Boesing\Expressive\Cors\Middleware\CorsMiddleware;
use LogicException;
use Zend\Expressive\Router\Middleware\DispatchMiddleware;
use Zend\Expressive\Router\Middleware\RouteMiddleware;

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
