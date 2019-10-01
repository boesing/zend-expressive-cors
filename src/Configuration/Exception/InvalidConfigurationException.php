<?php

declare(strict_types=1);

namespace Boesing\Expressive\Cors\Configuration\Exception;

use Boesing\Expressive\Cors\Exception\AbstractInvalidArgumentException;

final class InvalidConfigurationException extends AbstractInvalidArgumentException
{
    public static function create(string $message)
    {
        return new self($message);
    }
}
