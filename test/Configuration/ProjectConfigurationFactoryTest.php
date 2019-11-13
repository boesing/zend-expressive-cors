<?php
declare(strict_types=1);

namespace Boesing\Expressive\CorsTest\Configuration;

use Boesing\Expressive\Cors\Configuration\ConfigurationInterface;
use Boesing\Expressive\Cors\Configuration\ProjectConfiguration;
use Boesing\Expressive\Cors\Configuration\ProjectConfigurationFactory;
use Boesing\Expressive\CorsTest\AbstractFactoryTest;

final class ProjectConfigurationFactoryTest extends AbstractFactoryTest
{

    protected function dependencies(): array
    {
        return [
            'config' => [ConfigurationInterface::CONFIGURATION_IDENTIFIER => ['exposed_headers' => ['X-Foo']]],
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
        $this->assertEquals(['X-Foo'], $instance->exposedHeaders());
    }
}
