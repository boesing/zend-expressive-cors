<?php

declare(strict_types=1);

namespace Boesing\Expressive\Cors\Configuration;

use Boesing\Expressive\Cors\Configuration\Exception\InvalidConfigurationException;
use Boesing\Expressive\Cors\Exception\BadMethodCallException;
use Boesing\Expressive\Cors\Service\CorsMetadata;
use Webmozart\Assert\Assert;

use function array_unique;
use function array_values;
use function call_user_func;
use function in_array;
use function is_callable;
use function lcfirst;
use function sprintf;
use function str_replace;
use function ucfirst;
use function ucwords;

abstract class AbstractConfiguration implements ConfigurationInterface
{
    /** @var string[] */
    protected $allowedOrigins = [];

    /** @var string[] */
    protected $allowedMethods = [];

    /** @var string[] */
    protected $allowedHeaders = [];

    /** @var string */
    protected $allowedMaxAge = '';

    /** @var bool */
    protected $credentialsAllowed = false;

    /** @var string[] */
    protected $exposedHeaders = [];

    public function __construct(array $parameters)
    {
        try {
            $this->exchangeArray($parameters);
        } catch (BadMethodCallException $exception) {
            throw InvalidConfigurationException::create($exception->getMessage());
        }
    }

    /**
     * @param array<string,mixed> $data
     */
    public function exchangeArray(array $data) : self
    {
        $instance = clone $this;

        foreach ($data as $property => $value) {
            $property = lcfirst(str_replace('_', '', ucwords($property, '_')));
            $setter   = sprintf('set%s', ucfirst($property));
            if (! is_callable([$this, $setter])) {
                throw BadMethodCallException::fromMissingSetterMethod($property, $setter);
            }

            call_user_func([$this, $setter], $value);
        }

        return $instance;
    }

    public function setAllowedOrigins(array $origins) : void
    {
        Assert::allString($origins);

        $origins = array_values(array_unique($origins));

        if (in_array('*', $origins, true)) {
            $origins = ['*'];
        }

        $this->allowedOrigins = $origins;
    }

    public function allowedMethods() : array
    {
        return $this->allowedMethods;
    }

    public function setAllowedHeaders(array $headers) : void
    {
        Assert::allString($headers);
        $this->allowedHeaders = $headers;
    }

    public function allowedHeaders() : array
    {
        return $this->allowedHeaders;
    }

    public function setAllowedMaxAge(string $age) : void
    {
        if ($age) {
            Assert::numeric($age);
        }

        $this->allowedMaxAge = $age;
    }

    public function allowedMaxAge() : string
    {
        return $this->allowedMaxAge;
    }

    public function setExposedHeaders(array $headers) : void
    {
        Assert::allString($headers);
        $this->exposedHeaders = array_values(array_unique($headers));
    }

    public function exposedHeaders() : array
    {
        return $this->exposedHeaders;
    }

    public function credentialsAllowed() : bool
    {
        return $this->credentialsAllowed;
    }

    public function setCredentialsAllowed(bool $credentialsAllowed) : void
    {
        $this->credentialsAllowed = $credentialsAllowed;
    }

    /**
     * @return string[]
     */
    public function allowedOrigins() : array
    {
        return $this->allowedOrigins;
    }

    /**
     * @param array<int|string,string> $methods
     *
     * @return array<int,string>
     */
    protected function normalizeRequestMethods(array $methods) : array
    {
        Assert::allOneOf($methods, CorsMetadata::ALLOWED_REQUEST_METHODS);

        return array_values(array_unique($methods));
    }
}
