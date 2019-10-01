<?php

declare(strict_types=1);

namespace Boesing\Expressive\Cors\Configuration;

use function array_unique;
use function array_values;

final class ProjectConfiguration extends AbstractConfiguration
{
    public function setAllowedMethods(array $methods) : void
    {
        $methods              = $this->normalizeRequestMethods($methods);
        $this->allowedMethods = array_values(array_unique($methods));
    }
}
