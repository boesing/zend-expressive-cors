<?php

declare(strict_types=1);

namespace Boesing\Expressive\Cors\Configuration;

use Psr\Container\ContainerInterface;

final class ProjectConfigurationFactory
{
    public function __invoke(ContainerInterface $container)
    {
        return new ProjectConfiguration(
            $container->get('config')[ProjectConfiguration::CONFIGURATION_IDENTIFIER] ?? []
        );
    }
}
