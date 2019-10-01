<?php

declare(strict_types=1);

namespace Boesing\Expressive\Cors\Service;

use Boesing\Expressive\Cors\Exception\InvalidOriginValueException;
use Fig\Http\Message\RequestMethodInterface;
use InvalidArgumentException;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\UriInterface;

use function strtoupper;
use function trim;

final class Cors implements CorsInterface
{
    /** @var UriFactoryInterface */
    private $uriFactory;

    public function __construct(UriFactoryInterface $uriFactory)
    {
        $this->uriFactory = $uriFactory;
    }

    public function isPreflightRequest(ServerRequestInterface $request) : bool
    {
        return $this->isCorsRequest($request)
            && strtoupper($request->getMethod()) === RequestMethodInterface::METHOD_OPTIONS
            && $request->hasHeader('Access-Control-Request-Method');
    }

    public function isCorsRequest(ServerRequestInterface $request) : bool
    {
        $origin = $this->origin($request);
        if (! $origin instanceof UriInterface) {
            return false;
        }

        $uri = $request->getUri();

        return $uri->getScheme() !== $origin->getScheme()
            || $uri->getPort() !== $origin->getPort()
            || $uri->getHost() !== $origin->getHost();
    }

    private function origin(ServerRequestInterface $request) : ?UriInterface
    {
        $origin = $request->getHeaderLine('Origin');

        if (trim($origin) === '') {
            return null;
        }

        try {
            return $this->uriFactory->createUri($origin);
        } catch (InvalidArgumentException $exception) {
            throw InvalidOriginValueException::fromThrowable($origin, $exception);
        }
    }

    public function metadata(ServerRequestInterface $request) : CorsMetadata
    {
        return new CorsMetadata(
            $this->origin($request),
            $request->getUri(),
            strtoupper($request->getHeaderLine('Access-Control-Request-Method'))
        );
    }
}
