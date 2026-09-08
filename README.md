# chubbyphp-negotiation

[![CI](https://github.com/chubbyphp/chubbyphp-negotiation/actions/workflows/ci.yml/badge.svg)](https://github.com/chubbyphp/chubbyphp-negotiation/actions/workflows/ci.yml)
[![Coverage Status](https://coveralls.io/repos/github/chubbyphp/chubbyphp-negotiation/badge.svg?branch=master)](https://coveralls.io/github/chubbyphp/chubbyphp-negotiation?branch=master)
[![Mutation testing badge](https://img.shields.io/endpoint?style=flat&url=https%3A%2F%2Fbadge-api.stryker-mutator.io%2Fgithub.com%2Fchubbyphp%2Fchubbyphp-negotiation%2Fmaster)](https://dashboard.stryker-mutator.io/reports/github.com/chubbyphp/chubbyphp-negotiation/master)
[![Latest Stable Version](https://poser.pugx.org/chubbyphp/chubbyphp-negotiation/v)](https://packagist.org/packages/chubbyphp/chubbyphp-negotiation)
[![Total Downloads](https://poser.pugx.org/chubbyphp/chubbyphp-negotiation/downloads)](https://packagist.org/packages/chubbyphp/chubbyphp-negotiation)
[![Monthly Downloads](https://poser.pugx.org/chubbyphp/chubbyphp-negotiation/d/monthly)](https://packagist.org/packages/chubbyphp/chubbyphp-negotiation)

[![bugs](https://sonarcloud.io/api/project_badges/measure?project=chubbyphp_chubbyphp-negotiation&metric=bugs)](https://sonarcloud.io/dashboard?id=chubbyphp_chubbyphp-negotiation)
[![code_smells](https://sonarcloud.io/api/project_badges/measure?project=chubbyphp_chubbyphp-negotiation&metric=code_smells)](https://sonarcloud.io/dashboard?id=chubbyphp_chubbyphp-negotiation)
[![coverage](https://sonarcloud.io/api/project_badges/measure?project=chubbyphp_chubbyphp-negotiation&metric=coverage)](https://sonarcloud.io/dashboard?id=chubbyphp_chubbyphp-negotiation)
[![duplicated_lines_density](https://sonarcloud.io/api/project_badges/measure?project=chubbyphp_chubbyphp-negotiation&metric=duplicated_lines_density)](https://sonarcloud.io/dashboard?id=chubbyphp_chubbyphp-negotiation)
[![ncloc](https://sonarcloud.io/api/project_badges/measure?project=chubbyphp_chubbyphp-negotiation&metric=ncloc)](https://sonarcloud.io/dashboard?id=chubbyphp_chubbyphp-negotiation)
[![sqale_rating](https://sonarcloud.io/api/project_badges/measure?project=chubbyphp_chubbyphp-negotiation&metric=sqale_rating)](https://sonarcloud.io/dashboard?id=chubbyphp_chubbyphp-negotiation)
[![alert_status](https://sonarcloud.io/api/project_badges/measure?project=chubbyphp_chubbyphp-negotiation&metric=alert_status)](https://sonarcloud.io/dashboard?id=chubbyphp_chubbyphp-negotiation)
[![reliability_rating](https://sonarcloud.io/api/project_badges/measure?project=chubbyphp_chubbyphp-negotiation&metric=reliability_rating)](https://sonarcloud.io/dashboard?id=chubbyphp_chubbyphp-negotiation)
[![security_rating](https://sonarcloud.io/api/project_badges/measure?project=chubbyphp_chubbyphp-negotiation&metric=security_rating)](https://sonarcloud.io/dashboard?id=chubbyphp_chubbyphp-negotiation)
[![sqale_index](https://sonarcloud.io/api/project_badges/measure?project=chubbyphp_chubbyphp-negotiation&metric=sqale_index)](https://sonarcloud.io/dashboard?id=chubbyphp_chubbyphp-negotiation)
[![vulnerabilities](https://sonarcloud.io/api/project_badges/measure?project=chubbyphp_chubbyphp-negotiation&metric=vulnerabilities)](https://sonarcloud.io/dashboard?id=chubbyphp_chubbyphp-negotiation)


## Description

A small, dependency-light content negotiation library for [PSR-7][8] requests.

It picks the best match between what the client asks for and what your application supports:

 * `Accept` → which media type to respond with
 * `Accept-Language` → which locale to respond in
 * `Content-Type` → whether the request body can be parsed

Each negotiator returns a `NegotiatedValue` (the matched value plus its header attributes such as `q` or `charset`),
or `null` when nothing matches. Optional [PSR-15][9] middlewares turn a failed negotiation into a `406 Not Acceptable`
or `415 Unsupported Media Type` response and expose the negotiated value as a request attribute.

## Requirements

 * php: ^8.3
 * psr/http-message: ^1.1|^2.0

## Suggest

 * chubbyphp/chubbyphp-container: ^2.5.2 (for `NegotiationServiceFactory`)
 * chubbyphp/chubbyphp-http-exception: ^1.3.4 (required by the middlewares)
 * chubbyphp/chubbyphp-laminas-config-factory: ^1.5.3 (for the laminas-style `ServiceFactory` classes)
 * pimple/pimple: ^3.6.2 (for `NegotiationServiceProvider`)
 * psr/http-server-middleware: ^1.0.2 (required by the middlewares)

## Installation

Through [Composer](http://getcomposer.org) as [chubbyphp/chubbyphp-negotiation][1].

```sh
composer require chubbyphp/chubbyphp-negotiation "^2.3"
```

## Usage

All negotiators share the same contract: pass the supported values to the constructor, call `negotiate($request)`
and receive a `NegotiatedValueInterface` (value + header attributes) or `null` when nothing matches.
The middlewares wrap a negotiator, store the result as a request attribute and throw an `HttpException` on failure.

Each section below shows the minimal call; the linked page documents the matching rules, edge cases and error data.

### AcceptLanguageNegotiator

Negotiates `Accept-Language`. Exact locale first, then the language of a regional locale (`en-US` → `en`), then `*`.
[Full documentation][14]

```php
$negotiator = new AcceptLanguageNegotiator(['en', 'de']);

$value = $negotiator->negotiate($request); // 'Accept-Language: de,en-US;q=0.7,en;q=0.3'
$value->getValue();                        // 'de'
$value->getAttributes();                   // ['q' => '1.0']
```

### AcceptLanguageMiddleware

Stores the negotiated locale in the request attribute `acceptLanguage`, or throws `406 Not Acceptable`.
[Full documentation][15]

```php
$middleware = new AcceptLanguageMiddleware(new AcceptLanguageNegotiator(['en', 'de']));

$response = $middleware->process($request, $handler); // $request->getAttribute('acceptLanguage') inside $handler
```

### AcceptNegotiator

Negotiates `Accept`. Exact media type first, then structured suffix (`+json`), then `type/*`, then `*/*`.
[Full documentation][16]

```php
$negotiator = new AcceptNegotiator(['application/json', 'application/xml', 'application/x-yaml']);

$value = $negotiator->negotiate($request); // 'Accept: text/html,application/xml;q=0.9,*/*;q=0.8'
$value->getValue();                        // 'application/xml'
$value->getAttributes();                   // ['q' => '0.9']
```

### AcceptMiddleware

Stores the negotiated media type in the request attribute `accept`, or throws `406 Not Acceptable`.
[Full documentation][17]

```php
$middleware = new AcceptMiddleware(new AcceptNegotiator(['application/json', 'application/xml']));

$response = $middleware->process($request, $handler); // $request->getAttribute('accept') inside $handler
```

### ContentTypeNegotiator

Negotiates `Content-Type`. Exact media type first, then structured suffix (`application/vnd.api+json` → `application/json`).
Header parameters such as `charset` are returned as attributes.
[Full documentation][18]

```php
$negotiator = new ContentTypeNegotiator(['application/json', 'application/xml', 'application/x-yaml']);

$value = $negotiator->negotiate($request); // 'Content-Type: application/xml; charset=UTF-8'
$value->getValue();                        // 'application/xml'
$value->getAttributes();                   // ['charset' => 'UTF-8']
```

### ContentTypeMiddleware

Stores the negotiated media type in the request attribute `contentType`, or throws `415 Unsupported Media Type`.
[Full documentation][19]

```php
$middleware = new ContentTypeMiddleware(new ContentTypeNegotiator(['application/json', 'application/xml']));

$response = $middleware->process($request, $handler); // $request->getAttribute('contentType') inside $handler
```

### NegotiationServiceFactory

Registers all negotiators and middlewares in a [chubbyphp/chubbyphp-container][10] under `negotiator.*` ids.
The supported values are read from `negotiator.*.values` services, which default to `[]`.
[Full documentation][20]

```php
$container = new Container();
$container->factories((new NegotiationServiceFactory())());
$container->factory('negotiator.acceptNegotiator.values', static fn (): array => ['application/json']);

$container->get('negotiator.acceptMiddleware')->process($request, $handler);
```

### NegotiationServiceProvider

Registers the same services in a [Pimple][11] container, using the same service ids.
[Full documentation][21]

```php
$container = new Container();
$container->register(new NegotiationServiceProvider());
$container['negotiator.acceptNegotiator.values'] = ['application/json'];

$container['negotiator.acceptMiddleware']->process($request, $handler);
```

### ServiceFactory

Invokable factories built on [chubbyphp/chubbyphp-laminas-config-factory][12] for
[laminas-servicemanager][13] style containers. Each factory can be used unnamed or with a name
(`[Factory::class, 'name']`) to register several independent instances.

 * [AcceptLanguageMiddlewareFactory][2]
 * [AcceptLanguageNegotiatorFactory][3]
 * [AcceptMiddlewareFactory][4]
 * [AcceptNegotiatorFactory][5]
 * [ContentTypeMiddlewareFactory][6]
 * [ContentTypeNegotiatorFactory][7]

## Copyright

2026 Dominik Zogg

[1]: https://packagist.org/packages/chubbyphp/chubbyphp-negotiation

[2]: doc/ServiceFactory/AcceptLanguageMiddlewareFactory.md
[3]: doc/ServiceFactory/AcceptLanguageNegotiatorFactory.md
[4]: doc/ServiceFactory/AcceptMiddlewareFactory.md
[5]: doc/ServiceFactory/AcceptNegotiatorFactory.md
[6]: doc/ServiceFactory/ContentTypeMiddlewareFactory.md
[7]: doc/ServiceFactory/ContentTypeNegotiatorFactory.md

[8]: https://www.php-fig.org/psr/psr-7/
[9]: https://www.php-fig.org/psr/psr-15/
[10]: https://github.com/chubbyphp/chubbyphp-container
[11]: https://github.com/silexphp/Pimple
[12]: https://github.com/chubbyphp/chubbyphp-laminas-config-factory
[13]: https://docs.laminas.dev/laminas-servicemanager/

[14]: doc/AcceptLanguageNegotiator.md
[15]: doc/Middleware/AcceptLanguageMiddleware.md
[16]: doc/AcceptNegotiator.md
[17]: doc/Middleware/AcceptMiddleware.md
[18]: doc/ContentTypeNegotiator.md
[19]: doc/Middleware/ContentTypeMiddleware.md
[20]: doc/ServiceFactory/NegotiationServiceFactory.md
[21]: doc/ServiceProvider/NegotiationServiceProvider.md
