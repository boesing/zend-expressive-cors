<?php
declare(strict_types=1);

namespace Boesing\Expressive\CorsTest\Config;

use Boesing\Expressive\Cors\Configuration\ConfigurationInterface;
use Boesing\Expressive\Cors\Configuration\ProjectConfiguration;
use Boesing\Expressive\Cors\Configuration\ProjectConfigurationFactory;
use Boesing\Expressive\CorsTest\AbstractFactoryTest;

final class ProjectConfigurationFactoryTest extends AbstractFactoryTest
{

    /**
     * @return string[]
     */
    protected function dependencies(): array
    {
        return [
            'config' => [ConfigurationInterface::CONFIGURATION_IDENTIFIER => ['allowed_methods' => ['GET']]],
        ];
    }

    protected function factory(): callable
    {
        return new ProjectConfigurationFactory();
    }

    /**
     * Implement this for post creation assertions.
     */
    protected function postCreationAssertions($instance): void
    {
        $this->assertInstanceOf(ProjectConfiguration::class, $instance);
        /** @var ProjectConfiguration $instance */
        $this->assertEquals(['GET'], $instance->allowedMethods());
    }
}
