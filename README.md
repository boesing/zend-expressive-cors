# zend-expressive-cors

[![Build Status](https://travis-ci.org/boesing/zend-expressive-cors.svg?branch=master)](https://travis-ci.org/boesing/zend-expressive-cors)
[![Coverage Status](https://coveralls.io/repos/github/boesing/zend-expressive-cors/badge.svg?branch=master)](https://coveralls.io/github/boesing/zend-expressive-cors?branch=master)

CORS subcomponent for [Expressive](https://github.com/mezzio/mezzio).


This extension creates CORS details for your application. If the `CorsMiddleware` detects a `CORS preflight`, the middleware will start do detect the proper `CORS` configuration.
The `Router` is being used to detect every allowed request method by executing a route match with all possible request methods. Therefore, for every preflight request, there is at least one `Router` request (depending on the configuration of the route, it might be just one or we are executing a check for **every** request method).

Here is a list of the request methods being checked for the `CORS preflight` information:

- DELETE
- GET
- HEAD
- OPTIONS
- PATCH
- POST
- PUT
- TRACE

The order of the headers might vary, depending on what request method is being requested with the `CORS preflight` request.
In the end, the response contains **every** possible request method of the route due to what the router tells the `ConfigurationLocator`.


The allowed origins can be configured as strings which can be matched with [`fnmatch`](https://www.php.net/manual/en/function.fnmatch.php). Therefore, wildcards are possible.

## Installation

```bash
$ composer require boesing/zend-expressive-cors
```

## Configuration

There are 2 ways of configuring CORS in your project. Either create a global configuration file like `cors.global.php` or add a route specific configuration.

On the project level, you can only configure the following Headers:

| Configuration | Type | Header
|:-------------|:-------------:|:-----:
| `allowed_origins` | string[] | Access-Control-Allow-Origin       
| `allowed_headers` | string[] | Access-Control-Allow-Headers      
| `allowed_max_age` | string (TTL in seconds) | Access-Control-Allowed-Max-Age    
| `credentials_allowed` | bool | Access-Control-Allow-Credentials
| `exposed_headers` | string[] | Access-Control-Exposed-Headers


On the route level, you can configure all of the projects configuration settings and if the configuration of the route should either override the project configuration (default) or merge it.


| Configuration | Type | Header
|:------------- |:-------------:|:-----:
| `overrides_project_configuration` | bool | -
| `explicit` | bool | -
| `allowed_origins` | string[] | Access-Control-Allow-Origin       
| `allowed_headers` | string[] | Access-Control-Allow-Headers      
| `allowed_max_age` | string (TTL in seconds) | Access-Control-Allowed-Max-Age    
| `credentials_allowed` | bool | Access-Control-Allow-Credentials
| `exposed_headers` | string[] | Access-Control-Exposed-Headers
 
The parameter `overrides_project_configuration` handles the way how the configuration is being merged. The default setting is `true` to ensure that a route configuration has to specify every information it will provide.

The parameter `explicit` tells the `ConfigurationLocator` to stop trying other request methods to match the same route because there wont be any other method.

### Examples for project configurations
#### Allow every origin
```php
<?php
declare(strict_types=1);

use Boesing\Mezzio\Cors\Configuration\ConfigurationInterface;

return [
    ConfigurationInterface::CONFIGURATION_IDENTIFIER => [
        'allowed_origins' => [ConfigurationInterface::ANY_ORIGIN], // Allow any origin
        'allowed_headers' => [], // No custom headers allowed
        'allowed_max_age' => '600', // 10 minutes
        'credentials_allowed' => true, // Allow cookies
        'exposed_headers' => ['X-Custom-Header'], // Tell client that the API will always return this header  
    ],
];
```

#### Allow every origin from a specific domain and its subdomains
```php
<?php
declare(strict_types=1);

use Boesing\Mezzio\Cors\Configuration\ConfigurationInterface;

return [
    ConfigurationInterface::CONFIGURATION_IDENTIFIER => [
        'allowed_origins' => ['example.com', '*.example.com'],
        'allowed_headers' => [], // No custom headers allowed
        'allowed_max_age' => '3600', // 60 minutes
        'credentials_allowed' => false, // Disallow cookies
        'exposed_headers' => [], // No headers are exposed  
    ],
];
```


### Examples for route configurations
#### Make the configuration explicit to avoid multiple router match requests
```php
<?php
declare(strict_types=1);

use Boesing\Mezzio\Cors\Configuration\ConfigurationInterface;
use Boesing\Mezzio\Cors\Configuration\RouteConfigurationInterface;

return [
    ConfigurationInterface::CONFIGURATION_IDENTIFIER => [
        'allowed_origins' => ['example.com', '*.example.com'],
        'allowed_headers' => ['X-Project-Header'],
        'exposed_headers' => ['X-Some-Header'],
        'allowed_max_age' => '3600',
        'credentials_allowed' => true,
    ],
    'routes' => [
          [
            'name' => 'foo-get',
            'path' => '/foo',
            'middleware' => [
                // ...
            ],
            'options' => [
                'defaults' => [
                    RouteConfigurationInterface::PARAMETER_IDENTIFIER => [
                        'explicit' => true,
                        'allowed_origins' => ['someotherdomain.com'],
                        'allowed_headers' => ['X-Specific-Header-For-Foo-Endpoint'],
                        'allowed_max_age' => '3600',
                    ],
                ],
            ],
            'allowed_methods' => ['GET'],
        ],
        [
            'name' => 'foo-delete',
            'path' => '/foo',
            'middleware' => [
                // ...
            ],
            'allowed_methods' => ['DELETE'],
        ],
    ],
];
```

Result of this configuration for the `CORS preflight` of `/foo` for the upcoming `GET` request will look like this:

| Configuration | Parameter |
|:------------- |:-------------:|
| `allowed_origins` | `['someotherdomain.com']` |
| `allowed_headers` | `['X-Specific-Header-For-Foo-Endpoint']` |
| `allowed_max_age` | `3600` |
| `exposed_headers` | `[]` |
| `credentials_allowed` | `false`|
| `allowed_methods` | `['GET']` |


**Did you note the missing `DELETE`? This is because of the `explicit` flag! Also note the empty `exposed_headers` which is due to the project overriding (`overrides_project_configuration`) parameter.**


#### Enable project merging
```php
<?php
declare(strict_types=1);

use Boesing\Mezzio\Cors\Configuration\ConfigurationInterface;
use Boesing\Mezzio\Cors\Configuration\RouteConfigurationInterface;

return [
    ConfigurationInterface::CONFIGURATION_IDENTIFIER => [
        'allowed_origins' => ['example.com', '*.example.com'],
        'allowed_headers' => ['X-Project-Header'],
        'exposed_headers' => ['X-Some-Header'],
        'allowed_max_age' => '3600',
    ],
    'routes' => [
          [
            'name' => 'foo-get',
            'path' => '/foo',
            'middleware' => [
                // ...
            ],
            'options' => [
                'defaults' => [
                    RouteConfigurationInterface::PARAMETER_IDENTIFIER => [
                        'overrides_project_configuration' => false,
                        'allowed_origins' => [RouteConfigurationInterface::ANY_ORIGIN],
                        'allowed_headers' => ['X-Specific-Header-For-Foo-Endpoint'],
                        'allowed_max_age' => '7200',
                        'credentials_allowed' => true,
                    ],
                ],
            ],
            'allowed_methods' => ['GET'],
        ],
        [
            'name' => 'foo-delete',
            'path' => '/foo',
            'middleware' => [
                // ...
            ],
            'allowed_methods' => ['DELETE'],
        ],
    ],
];
```

Result of this configuration for the `CORS preflight` of `/foo` for the upcoming `GET` request will look like this:

| Configuration | Parameter |
|:-------------|:-------------:|
| `allowed_origins` | `[RouteConfigurationInterface::ANY_ORIGIN]` |
| `allowed_headers` | `['X-Specific-Header-For-Foo-Endpoint', 'X-Project-Header']` |
| `allowed_max_age` | `7200` |
| `exposed_headers` | `['X-Some-Header']` |
| `credentials_allowed` | `true`|
| `allowed_methods` | `['GET', 'DELETE']` |

**Did you note the `ANY_ORIGIN` detail? This is, because if `ANY_ORIGIN` is allowed for an endpoint, we remove** 
