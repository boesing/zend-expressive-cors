<?php
declare(strict_types=1);

namespace Boesing\Mezzio\CorsTest\Configuration;

use Boesing\Mezzio\Cors\Configuration\ConfigurationInterface;
use Boesing\Mezzio\Cors\Configuration\ProjectConfiguration;
use Boesing\Mezzio\Cors\Configuration\ProjectConfigurationFactory;
use Boesing\Mezzio\CorsTest\AbstractFactoryTest;

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
