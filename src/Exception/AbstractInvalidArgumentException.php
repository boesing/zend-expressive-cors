<?php

declare(strict_types=1);

namespace Boesing\Expressive\Cors\Exception;

use InvalidArgumentException as BaseInvalidArgumentException;

abstract class AbstractInvalidArgumentException extends BaseInvalidArgumentException implements ExceptionInterface
{
}
