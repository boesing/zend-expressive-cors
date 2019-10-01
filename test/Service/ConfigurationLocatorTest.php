<?php

declare(strict_types=1);

namespace Boesing\Expressive\CorsTest\Service;

use Boesing\Expressive\Cors\Configuration\ConfigurationInterface;
use Boesing\Expressive\Cors\Configuration\RouteConfigurationFactoryInterface;
use Boesing\Expressive\Cors\Configuration\RouteConfigurationInterface;
use Boesing\Expressive\Cors\Service\ConfigurationLocator;
use Boesing\Expressive\Cors\Service\CorsMetadata;
use Fig\Http\Message\RequestMethodInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use function strpos;
use Zend\Expressive\Router\RouteResult;
use Zend\Expressive\Router\RouterInterface;
use function array_diff;
use function array_fill;
use function array_merge;

final class ConfigurationLocatorTest extends TestCase
{

    /** @var ConfigurationLocator */
    private $locator;

    /** @var ConfigurationInterface&MockObject */
    private $projectConfiguration;

    /** @var MockObject&ServerRequestFactoryInterface */
    private $requestFactory;

    /** @var MockObject&RouterInterface */
    private $router;

    /** @var RouteConfigurationFactoryInterface&MockObject */
    private $routeConfigurationFactory;

    public function testWillLocateProjectConfigurationDueToUnknownRoute(): void
    {
        $originUri = $this->createMock(UriInterface::class);
        $requestUri = $this->createMock(UriInterface::class);
        $metadata = new CorsMetadata($originUri, $requestUri, RequestMethodInterface::METHOD_GET);

        $request = $this->createMock(ServerRequestInterface::class);
        $this->requestFactory
            ->expects($this->once())
            ->method('createServerRequest')
            ->with(RequestMethodInterface::METHOD_GET, $requestUri)
            ->willReturn($request);

        $routeResult = $this->createMock(RouteResult::class);
        $routeResult
            ->expects($this->once())
            ->method('isFailure')
            ->willReturn(true);

        $this->router
            ->expects($this->once())
            ->method('match')
            ->willReturn($routeResult);

        $routeConfiguration = $this->createMock(RouteConfigurationInterface::class);

        $this->routeConfigurationFactory
            ->expects($this->once())
            ->method('__invoke')
            ->with([])
            ->willReturn($routeConfiguration);

        $routeConfiguration
            ->expects($this->once())
            ->method('mergeWithConfiguration')
            ->with($this->projectConfiguration)
            ->willReturn($routeConfiguration);

        $routeConfiguration
            ->expects($this->once())
            ->method('explicit')
            ->willReturn(true);

        $located = $this->locator->locate($metadata);
        $this->assertEquals($routeConfiguration, $located);
    }

    public function testWillLocateProjectConfigDueRoutesWithoutConfigWithMergedRouteResultsMethods(): void
    {
        $originUri = $this->createMock(UriInterface::class);
        $requestUri = $this->createMock(UriInterface::class);
        $metadata = new CorsMetadata($originUri, $requestUri, RequestMethodInterface::METHOD_GET);

        $request = $this->createMock(ServerRequestInterface::class);
        $this->requestFactory
            ->expects($this->once())
            ->method('createServerRequest')
            ->with(RequestMethodInterface::METHOD_GET, $requestUri)
            ->willReturn($request);

        $routeResult = $this->createMock(RouteResult::class);
        $routeResult
            ->expects($this->once())
            ->method('isFailure')
            ->willReturn(false);

        $this->router
            ->expects($this->once())
            ->method('match')
            ->willReturn($routeResult);

        $routeResult
            ->expects($this->once())
            ->method('getAllowedMethods')
            ->willReturn([RequestMethodInterface::METHOD_GET]);

        $routeConfiguration = $this->createMock(RouteConfigurationInterface::class);

        $routeConfiguration
            ->expects($this->once())
            ->method('mergeWithConfiguration')
            ->with($this->projectConfiguration)
            ->willReturn($routeConfiguration);

        $routeConfiguration
            ->expects($this->once())
            ->method('withRequestMethods')
            ->with([RequestMethodInterface::METHOD_GET])
            ->willReturn($routeConfiguration);

        $this->routeConfigurationFactory
            ->expects($this->once())
            ->method('__invoke')
            ->willReturn($routeConfiguration);

        $routeConfiguration
            ->expects($this->once())
            ->method('explicit')
            ->willReturn(true);

        $located = $this->locator->locate($metadata);
        $this->assertEquals($routeConfiguration, $located);
    }

    public function testWillLocateRouteConfigDueRouteWithConfigButWithoutAllowedMethods(): void
    {
        $originUri = $this->createMock(UriInterface::class);
        $requestUri = $this->createMock(UriInterface::class);
        $metadata = new CorsMetadata($originUri, $requestUri, RequestMethodInterface::METHOD_GET);

        $request = $this->createMock(ServerRequestInterface::class);
        $this->requestFactory
            ->expects($this->once())
            ->method('createServerRequest')
            ->with('GET', $requestUri)
            ->willReturn($request);

        $routeResult = $this->createMock(RouteResult::class);
        $routeResult
            ->expects($this->once())
            ->method('isFailure')
            ->willReturn(false);

        $this->router
            ->expects($this->once())
            ->method('match')
            ->willReturn($routeResult);

        $routeResult
            ->expects($this->once())
            ->method('getAllowedMethods')
            ->willReturn([RequestMethodInterface::METHOD_GET]);

        $routeConfiguration = $this->createMock(RouteConfigurationInterface::class);

        $routeConfiguration
            ->expects($this->once())
            ->method('mergeWithConfiguration')
            ->with($this->projectConfiguration)
            ->willReturn($routeConfiguration);

        $routeConfiguration
            ->expects($this->once())
            ->method('withRequestMethods')
            ->with([RequestMethodInterface::METHOD_GET])
            ->willReturn($routeConfiguration);

        $this->routeConfigurationFactory
            ->expects($this->once())
            ->method('__invoke')
            ->with(['foo' => 'bar'])
            ->willReturn($routeConfiguration);

        $routeConfiguration
            ->expects($this->once())
            ->method('explicit')
            ->willReturn(true);

        $routeResult
            ->expects($this->once())
            ->method('getMatchedParams')
            ->willReturn([
                RouteConfigurationInterface::PARAMETER_IDENTIFIER => [
                    'foo' => 'bar',
                ],
            ]);

        $this->locator->locate($metadata);
    }

    public function testWillHandleRouteThatOverridesProjectConfiguration(): void
    {
        $originUri = $this->createMock(UriInterface::class);
        $requestUri = $this->createMock(UriInterface::class);
        $method = 'GET';

        $request = $this->createMock(ServerRequestInterface::class);

        $this->requestFactory
            ->expects($this->once())
            ->method('createServerRequest')
            ->with($method, $requestUri)
            ->willReturn($request);

        $metadata = new CorsMetadata($originUri, $requestUri, $method);

        $routeResult = $this->createMock(RouteResult::class);
        $routeResult
            ->expects($this->once())
            ->method('isFailure')
            ->willReturn(false);

        $routeResult
            ->expects($this->once())
            ->method('getMatchedParams')
            ->willReturn([RouteConfigurationInterface::PARAMETER_IDENTIFIER => []]);

        $routeResult
            ->expects($this->once())
            ->method('getAllowedMethods')
            ->willReturn([]);

        $routeConfiguration = $this->createMock(RouteConfigurationInterface::class);

        $routeConfiguration
            ->expects($this->once())
            ->method('overridesProjectConfiguration')
            ->willReturn(true);

        $routeConfiguration
            ->expects($this->never())
            ->method('mergeWithConfiguration');

        $routeConfiguration
            ->expects($this->once())
            ->method('withRequestMethods')
            ->willReturnSelf();

        $routeConfiguration
            ->expects($this->once())
            ->method('explicit')
            ->willReturn(true);

        $this->routeConfigurationFactory
            ->expects($this->once())
            ->method('__invoke')
            ->willReturn($routeConfiguration);

        $this->router
            ->expects($this->once())
            ->method('match')
            ->with($request)
            ->willReturn($routeResult);

        $config = $this->locator->locate($metadata);
        $this->assertEquals($routeConfiguration, $config);
    }

    public function testWillMergeFromMultipleMatchingRoutes(): void
    {
        $originUri = $this->createMock(UriInterface::class);
        $requestUri = $this->createMock(UriInterface::class);

        $method = 'GET';
        $metadata = new CorsMetadata($originUri, $requestUri, $method);

        $consecutiveParameters = $this->createServerRequestArguments($method, $requestUri);
        $request = $this->createMock(ServerRequestInterface::class);

        $this->requestFactory
            ->expects($this->any())
            ->method('createServerRequest')
            ->withConsecutive(
                ...$consecutiveParameters
            )
            ->willReturn($request);

        $matchingRouteResult = $this->createMock(RouteResult::class);
        $matchingRouteResult
            ->expects($this->any())
            ->method('isFailure')
            ->willReturn(false);

        $matchingRouteResult
            ->expects($this->once())
            ->method('getAllowedMethods')
            ->willReturn(['OPTIONS', 'HEAD']);

        $failedRouteResult = $this->createMock(RouteResult::class);
        $failedRouteResult
            ->expects($this->any())
            ->method('isFailure')
            ->willReturn(true);


        $routeConfigurationParameters = [
            RouteConfigurationInterface::PARAMETER_IDENTIFIER => [],
        ];
        $matchingRouteResultWithConfiguration = $this->createMock(RouteResult::class);
        $matchingRouteResultWithConfiguration
            ->expects($this->any())
            ->method('isFailure')
            ->willReturn(false);

        $matchingRouteResultWithConfiguration
            ->expects($this->once())
            ->method('getAllowedMethods')
            ->willReturn(['POST']);

        $matchingRouteResultWithConfiguration
            ->expects($this->once())
            ->method('getMatchedParams')
            ->willReturn($routeConfigurationParameters);

        $this->router
            ->expects($this->any())
            ->method('match')
            ->with($request)
            ->willReturnOnConsecutiveCalls(
                $failedRouteResult,
                $matchingRouteResult,
                $matchingRouteResultWithConfiguration,
                ...array_fill(0, count(CorsMetadata::ALLOWED_REQUEST_METHODS) - 3, $failedRouteResult)
            );

        $routeConfiguration = $this->createMock(RouteConfigurationInterface::class);
        $routeConfiguration
            ->expects($this->any())
            ->method('withRequestMethods')
            ->withConsecutive(
                [
                    ['OPTIONS', 'HEAD'],
                ],
                [
                    ['POST'],
                ]
            )
            ->willReturnSelf();

        $routeConfiguration
            ->expects($this->any())
            ->method('mergeWithConfiguration')
            ->willReturnSelf();

        $this->routeConfigurationFactory
            ->expects($this->any())
            ->method('__invoke')
            ->willReturn($routeConfiguration);

        $locatedConfiguration = $this->locator->locate($metadata);
        $this->assertSame($routeConfiguration, $locatedConfiguration);
    }

    private function createServerRequestArguments(string $initialRequestMethod, UriInterface $requestUri): array
    {
        $initialArgument = [$initialRequestMethod, $requestUri];
        $otherRequestMethods = array_diff(CorsMetadata::ALLOWED_REQUEST_METHODS, [$initialRequestMethod]);

        $consecutiveArguments = [];
        foreach ($otherRequestMethods as $requestMethod) {
            $consecutiveArguments[] = [$requestMethod, $requestUri];
        }

        return array_merge([$initialArgument], $consecutiveArguments);
    }

    public function testWillEarlyReturnExplicitRouteConfiguration(): void
    {
        $originUri = $this->createMock(UriInterface::class);
        $requestUri = $this->createMock(UriInterface::class);

        $method = 'GET';
        $metadata = new CorsMetadata($originUri, $requestUri, $method);

        $failedRouteResult = $this->createMock(RouteResult::class);
        $failedRouteResult
            ->expects($this->any())
            ->method('isFailure')
            ->willReturn(true);

        $matchingExplicitRouteResult = $this->createMock(RouteResult::class);
        $matchingExplicitRouteResult
            ->expects($this->any())
            ->method('isFailure')
            ->willReturn(false);

        $matchingExplicitRouteResult
            ->expects($this->once())
            ->method('getMatchedParams')
            ->willReturn([RouteConfigurationInterface::PARAMETER_IDENTIFIER => []]);

        $matchingExplicitRouteResult
            ->expects($this->once())
            ->method('getAllowedMethods')
            ->willReturn(['POST']);

        $routeConfiguration = $this->createMock(RouteConfigurationInterface::class);
        $routeConfiguration
            ->expects($this->any())
            ->method('mergeWithConfiguration')
            ->willReturnSelf();

        $routeConfiguration
            ->expects($this->once())
            ->method('withRequestMethods')
            ->with(['POST'])
            ->willReturnSelf();

        $this->routeConfigurationFactory
            ->expects($this->any())
            ->method('__invoke')
            ->willReturn($routeConfiguration);

        $routeConfiguration
            ->expects($this->any())
            ->method('explicit')
            ->willReturnOnConsecutiveCalls(false, true);

        $this
            ->router
            ->expects($this->any())
            ->method('match')
            ->willReturnOnConsecutiveCalls($failedRouteResult, $matchingExplicitRouteResult);

        $locatedConfiguration = $this->locator->locate($metadata);
        $this->assertSame($routeConfiguration, $locatedConfiguration);
    }

    protected function setUp()
    {
        parent::setUp();
        $this->projectConfiguration = $this->createMock(ConfigurationInterface::class);
        $this->requestFactory = $this->createMock(ServerRequestFactoryInterface::class);
        $this->router = $this->createMock(RouterInterface::class);
        $this->routeConfigurationFactory = $this->createMock(RouteConfigurationFactoryInterface::class);

        $this->locator = new ConfigurationLocator(
            $this->projectConfiguration,
            $this->requestFactory,
            $this->router,
            $this->routeConfigurationFactory
        );
    }
}