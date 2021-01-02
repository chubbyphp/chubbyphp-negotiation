# chubbyphp-negotiation

[![Build Status](https://api.travis-ci.org/chubbyphp/chubbyphp-negotiation.png?branch=master)](https://travis-ci.org/chubbyphp/chubbyphp-negotiation)
[![Coverage Status](https://coveralls.io/repos/github/chubbyphp/chubbyphp-negotiation/badge.svg?branch=master)](https://coveralls.io/github/chubbyphp/chubbyphp-negotiation?branch=master)
[![Infection MSI](https://badge.stryker-mutator.io/github.com/chubbyphp/chubbyphp-negotiation/master)](https://travis-ci.org/chubbyphp/chubbyphp-negotiation)
[![Latest Stable Version](https://poser.pugx.org/chubbyphp/chubbyphp-negotiation/v/stable.png)](https://packagist.org/packages/chubbyphp/chubbyphp-negotiation)
[![Total Downloads](https://poser.pugx.org/chubbyphp/chubbyphp-negotiation/downloads.png)](https://packagist.org/packages/chubbyphp/chubbyphp-negotiation)
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

 * php: ^7.4|^8.0
 * psr/http-message: ^1.0

## Suggest

 * chubbyphp/chubbyphp-container: ^1.0
 * pimple/pimple: ^3.3

## Installation

Through [Composer](http://getcomposer.org) as [chubbyphp/chubbyphp-negotiation][1].

```sh
composer require chubbyphp/chubbyphp-negotiation "^1.8"
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

$container->get('negotiator.acceptNegotiator')
    ->negotiate($request);

$container->get('negotiator.contentTypeNegotiator')
    ->negotiate($request);
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

$container['negotiator.acceptNegotiator']
    ->negotiate($request);

$container['negotiator.contentTypeNegotiator']
    ->negotiate($request);
```

### ServiceFactory

 * [AcceptLanguageNegotiatorFactory][2]
 * [AcceptNegotiatorFactory][3]
 * [ContentTypeNegotiatorFactory][4]

## Copyright

Dominik Zogg 2020

[1]: https://packagist.org/packages/chubbyphp/chubbyphp-negotiation

[2]: doc/ServiceFactory/AcceptLanguageNegotiatorFactory.md
[3]: doc/ServiceFactory/AcceptNegotiatorFactory.md
[4]: doc/ServiceFactory/ContentTypeNegotiatorFactory.md
