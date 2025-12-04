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

A simple negotiation library.

## Requirements

 * php: ^8.3
 * psr/http-message: ^1.1|^2.0

## Suggest

 * chubbyphp/chubbyphp-container: ^2.3
 * chubbyphp/chubbyphp-http-exception: ^1.2
 * chubbyphp/chubbyphp-laminas-config-factory: ^1.4
 * pimple/pimple: ^3.6
 * psr/http-server-middleware: ^1.0.2

## Installation

Through [Composer](http://getcomposer.org) as [chubbyphp/chubbyphp-negotiation][1].

```sh
composer require chubbyphp/chubbyphp-negotiation "^2.3"
```

## Usage

### AcceptLanguageNegotiator

```php
<?php

use Chubbyphp\Negotiation\AcceptLanguageNegotiator;

$request = ...;
$request->withHeader('Accept-Language', 'de,en-US;q=0.7,en;q=0.3');

$negotiator = new AcceptLanguageNegotiator(['en', 'de']);
$value = $negotiator->negotiate($request); // NegotiatedValue
$value->getValue(); // de
$value->getAttributes(); // ['q' => '1.0']
```

### AcceptLanguageMiddleware

```php
<?php

use Chubbyphp\Negotiation\Middleware\AcceptLanguageMiddleware;

$request = ...;
$request->withHeader('Accept-Language', 'de,en-US;q=0.7,en;q=0.3');

$middleware = new AcceptLanguageMiddleware($acceptLanguageNegotiator);
$response = $negotiator->process($request, $handler);
```

### AcceptNegotiator

```php
<?php

use Chubbyphp\Negotiation\AcceptNegotiator;

$request = ...;
$request->withHeader('Accept', 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q =0.8');

$negotiator = new AcceptNegotiator(['application/json', 'application/xml', 'application/x-yaml']);
$value = $negotiator->negotiate($request); // NegotiatedValue
$value->getValue(); // application/xml
$value->getAttributes(); // ['q' => '0.9']
```

### AcceptMiddleware

```php
<?php

use Chubbyphp\Negotiation\Middleware\AcceptMiddleware;

$request = ...;
$request->withHeader('Accept', 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q =0.8');

$middleware = new AcceptMiddleware($acceptNegotiator);
$response = $negotiator->process($request, $handler);
```

### ContentTypeNegotiator

```php
<?php

use Chubbyphp\Negotiation\ContentTypeNegotiator;

$request = ...;
$request->withHeader('Content-Type', 'application/xml; charset=UTF-8');

$negotiator = new ContentTypeNegotiator(['application/json', 'application/xml', 'application/x-yaml']);
$value = $negotiator->negotiate($request); // NegotiatedValue
$value->getValue(); // application/xml
$value->getAttributes(); // ['charset' => 'UTF-8']
```

### ContentTypeMiddleware

```php
<?php

use Chubbyphp\Negotiation\Middleware\ContentTypeMiddleware;

$request = ...;
$request->withHeader('Content-Type', 'application/xml; charset=UTF-8');

$middleware = new ContentTypeMiddleware($contentTypeNegotiator);
$response = $negotiator->process($request, $handler);
```

### NegotiationServiceFactory

```php
<?php

use Chubbyphp\Container\Container;
use Chubbyphp\Negotiation\ServiceFactory\NegotiationServiceFactory;
use Psr\Http\Message\ServerRequestInterface;

$container = new Container();
$container->factories((new NegotiationServiceFactory())());

$request = ...;

$container->get('negotiator.acceptNegotiator')
    ->negotiate($request);

$container->get('negotiator.acceptMiddleware')
    ->process($request, $handler);

$container->get('negotiator.acceptLanguageNegotiator')
    ->negotiate($request);

$container->get('negotiator.acceptLanguageMiddleware')
    ->process($request, $handler);

$container->get('negotiator.contentTypeNegotiator')
    ->negotiate($request);

$container->get('negotiator.contentTypeMiddleware')
    ->process($request, $handler);
```

### NegotiationServiceProvider

```php
<?php

use Chubbyphp\Negotiation\ServiceProvider\NegotiationServiceProvider;
use Pimple\Container;
use Psr\Http\Message\ServerRequestInterface;

$container = new Container();
$container->register(new NegotiationServiceProvider);

$request = ...;

$container['negotiator.acceptNegotiator']
    ->negotiate($request);

$container['negotiator.acceptMiddleware']
    ->process($request, $handler);

$container['negotiator.acceptLanguageNegotiator']
    ->negotiate($request);

$container['negotiator.acceptLanguageMiddleware']
    ->process($request, $handler);

$container['negotiator.contentTypeNegotiator']
    ->negotiate($request);

$container['negotiator.contentTypeMiddleware']
    ->process($request, $handler);
```

### ServiceFactory

 * [AcceptLanguageMiddlewareFactory][2]
 * [AcceptLanguageNegotiatorFactory][3]
 * [AcceptMiddlewareFactory][4]
 * [AcceptNegotiatorFactory][5]
 * [ContentTypeMiddlewareFactory][6]
 * [ContentTypeNegotiatorFactory][7]

## Copyright

2025 Dominik Zogg

[1]: https://packagist.org/packages/chubbyphp/chubbyphp-negotiation

[2]: doc/ServiceFactory/AcceptLanguageMiddlewareFactory.md
[3]: doc/ServiceFactory/AcceptLanguageNegotiatorFactory.md
[4]: doc/ServiceFactory/AcceptMiddlewareFactory.md
[5]: doc/ServiceFactory/AcceptNegotiatorFactory.md
[6]: doc/ServiceFactory/ContentTypeMiddlewareFactory.md
[7]: doc/ServiceFactory/ContentTypeNegotiatorFactory.md
