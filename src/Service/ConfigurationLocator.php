<?php

declare(strict_types=1);

namespace Boesing\Expressive\Cors\Service;

use Boesing\Expressive\Cors\Configuration\ConfigurationInterface;
use Boesing\Expressive\Cors\Configuration\RouteConfigurationFactoryInterface;
use Boesing\Expressive\Cors\Configuration\RouteConfigurationInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Zend\Expressive\Router\Route;
use Zend\Expressive\Router\RouteResult;
use Zend\Expressive\Router\RouterInterface;

use function array_diff;
use function array_merge;

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
    public function locate(CorsMetadata $metadata) : ?ConfigurationInterface
    {
        $factory       = $this->routeConfigurationFactory;
        $configuration = $factory([])->mergeWithConfiguration($this->configuration);

        // Move the requested method to the top so it will be the first one tried to match
        $requestMethods = array_merge([$metadata->requestedMethod], array_diff(
            CorsMetadata::ALLOWED_REQUEST_METHODS,
            [$metadata->requestedMethod]
        ));

        $anyRouteIsMatching = false;
        foreach ($requestMethods as $method) {
            $request = $this->requestFactory->createServerRequest($method, $metadata->requestedUri);
            $route   = $this->router->match($request);
            if ($route->isFailure()) {
                continue;
            }

            $anyRouteIsMatching         = true;
            $routeSpecificConfiguration = $this->configurationFromRoute($route);

            if ($routeSpecificConfiguration->explicit()) {
                return $routeSpecificConfiguration;
            }

            $configuration = $configuration->mergeWithConfiguration($routeSpecificConfiguration);
        }

        if (! $anyRouteIsMatching) {
            return null;
        }

        return $configuration;
    }

    private function configurationFromRoute(RouteResult $result) : RouteConfigurationInterface
    {
        $allowedMethods = $result->getAllowedMethods();
        if ($allowedMethods === Route::HTTP_METHOD_ANY) {
            $allowedMethods = CorsMetadata::ALLOWED_REQUEST_METHODS;
        }

        $explicit                  = $this->explicit($allowedMethods);
        $routeConfigurationFactory = $this->routeConfigurationFactory;

        $routeParameters = $result->getMatchedParams()[RouteConfigurationInterface::PARAMETER_IDENTIFIER] ?? null;
        if ($routeParameters === null) {
            return $routeConfigurationFactory(['explicit' => $explicit])
                ->mergeWithConfiguration($this->configuration)
                ->withRequestMethods($allowedMethods);
        }

        $routeParameters = ['explicit' => $explicit] + $routeParameters;

        $routeConfiguration = $routeConfigurationFactory($routeParameters)
            ->withRequestMethods($allowedMethods);

        if ($routeConfiguration->overridesProjectConfiguration()) {
            return $routeConfiguration;
        }

        return $routeConfiguration->mergeWithConfiguration($this->configuration);
    }

    private function explicit(array $allowedMethods) : bool
    {
        return $allowedMethods === CorsMetadata::ALLOWED_REQUEST_METHODS;
    }
}
