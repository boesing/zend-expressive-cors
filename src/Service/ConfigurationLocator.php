<?php

declare(strict_types=1);

namespace Boesing\Expressive\Cors\Service;

use Boesing\Expressive\Cors\Configuration\ConfigurationInterface;
use Boesing\Expressive\Cors\Configuration\RouteConfigurationFactoryInterface;
use Boesing\Expressive\Cors\Configuration\RouteConfigurationInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Zend\Expressive\Router\RouteResult;
use Zend\Expressive\Router\RouterInterface;

use function array_diff;

final class ConfigurationLocator implements ConfigurationLocatorInterface
{
    /** @var ConfigurationInterface */
    private $configuration;

    /** @var ServerRequestFactoryInterface */
    private $requestFactory;

    /** @var RouterInterface */
    private $router;

    /** @var RouteConfigurationFactoryInterface */
    private $routeConfigurationFactory;

    public function __construct(
        ConfigurationInterface $configuration,
        ServerRequestFactoryInterface $requestFactory,
        RouterInterface $router,
        RouteConfigurationFactoryInterface $routeConfigurationFactory
    ) {
        $this->configuration             = $configuration;
        $this->requestFactory            = $requestFactory;
        $this->router                    = $router;
        $this->routeConfigurationFactory = $routeConfigurationFactory;
    }

    /**
     * @inheritDoc
     */
    public function locate(CorsMetadata $metadata) : ConfigurationInterface
    {
        $configuration = $this->configuration($metadata);
        if ($configuration->explicit()) {
            return $configuration;
        }

        $requestMethods = array_diff(CorsMetadata::ALLOWED_REQUEST_METHODS, [$metadata->requestedMethod]);
        foreach ($requestMethods as $method) {
            $request                    = $this->requestFactory->createServerRequest($method, $metadata->requestedUri);
            $routeSpecificConfiguration = $this->configurationFromRoute($this->router->match($request));

            if ($routeSpecificConfiguration->explicit()) {
                return $routeSpecificConfiguration;
            }

            $configuration = $configuration->mergeWithConfiguration($routeSpecificConfiguration);
        }

        return $configuration;
    }

    /**
     * @param CorsMetadata $metadata
     *
     * @return ConfigurationInterface|RouteConfigurationInterface
     */
    private function configuration(CorsMetadata $metadata) : RouteConfigurationInterface
    {
        $request = $this->requestFactory->createServerRequest($metadata->requestedMethod, $metadata->requestedUri);
        $result  = $this->router->match($request);

        return $this->configurationFromRoute($result);
    }

    /**
     * @param RouteResult $result
     *
     * @return RouteConfigurationInterface|ConfigurationInterface
     */
    private function configurationFromRoute(RouteResult $result) : RouteConfigurationInterface
    {
        $routeConfigurationFactory = $this->routeConfigurationFactory;

        if ($result->isFailure()) {
            return $routeConfigurationFactory([])
                ->mergeWithConfiguration($this->configuration);
        }

        $allowedMethods = $result->getAllowedMethods();

        $routeParameters = $result->getMatchedParams()[RouteConfigurationInterface::PARAMETER_IDENTIFIER] ?? null;
        if ($routeParameters === null) {
            return $routeConfigurationFactory([])
                ->mergeWithConfiguration($this->configuration)
                ->withRequestMethods($allowedMethods);
        }

        $routeConfiguration = $routeConfigurationFactory($routeParameters)
            ->withRequestMethods($allowedMethods);

        if ($routeConfiguration->overridesProjectConfiguration()) {
            return $routeConfiguration;
        }

        return $routeConfiguration->mergeWithConfiguration($this->configuration);
    }
}
