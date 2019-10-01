<?php

declare(strict_types=1);

namespace Boesing\Expressive\Cors\Service;

use Boesing\Expressive\Cors\Configuration\ConfigurationInterface;
use Psr\Http\Message\ResponseInterface;

interface ResponseFactoryInterface
{
    /**
     * Creates a preflight response.
     */
    public function preflight(string $origin, ConfigurationInterface $config) : ResponseInterface;

    public function unauthorized(string $origin) : ResponseInterface;

    public function cors(ResponseInterface $response, string $origin, ConfigurationInterface $config);
}
