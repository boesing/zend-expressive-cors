<?php

declare(strict_types=1);

namespace Boesing\Expressive\Cors\Service;

use Boesing\Expressive\Cors\Configuration\ConfigurationInterface;
use Boesing\Expressive\Cors\Configuration\Exception\InvalidConfigurationException;

interface ConfigurationLocatorInterface
{
    /**
     * Should locate the configuration we have to apply to the response.
     *
     * @throws InvalidConfigurationException if there are more than one routes matching the request uri of
     *                                               the provided CorsMetadata
     */
    public function locate(CorsMetadata $metadata) : ?ConfigurationInterface;
}
