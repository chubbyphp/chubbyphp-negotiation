# chubbyphp-negotiation

[![Build Status](https://api.travis-ci.org/chubbyphp/chubbyphp-negotiation.png?branch=master)](https://travis-ci.org/chubbyphp/chubbyphp-negotiation)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/chubbyphp/chubbyphp-negotiation/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/chubbyphp/chubbyphp-negotiation/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/chubbyphp/chubbyphp-negotiation/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/chubbyphp/chubbyphp-negotiation/?branch=master)
[![Total Downloads](https://poser.pugx.org/chubbyphp/chubbyphp-negotiation/downloads.png)](https://packagist.org/packages/chubbyphp/chubbyphp-negotiation)
[![Monthly Downloads](https://poser.pugx.org/chubbyphp/chubbyphp-negotiation/d/monthly)](https://packagist.org/packages/chubbyphp/chubbyphp-negotiation)
[![Latest Stable Version](https://poser.pugx.org/chubbyphp/chubbyphp-negotiation/v/stable.png)](https://packagist.org/packages/chubbyphp/chubbyphp-negotiation)
[![Latest Unstable Version](https://poser.pugx.org/chubbyphp/chubbyphp-negotiation/v/unstable)](https://packagist.org/packages/chubbyphp/chubbyphp-negotiation)

## Description

A simple negotiation library.

## Requirements

 * php: ^7.2
 * psr/http-message: ~1.0

## Suggest

 * pimple/pimple: ~3.0

## Installation

Through [Composer](http://getcomposer.org) as [chubbyphp/chubbyphp-negotiation][1].

```sh
composer require chubbyphp/chubbyphp-negotiation "^1.3"
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

### NegotiationProvider

```php
<?php

use Chubbyphp\Negotiation\Provider\NegotiationProvider;
use Pimple\Container;
use Psr\Http\Message\ServerRequestInterface;

$container = new Container();
$container->register(new NegotiationProvider);

$request = ...;

$container['negotiator.acceptNegotiator']
    ->negotiate($request);

$container['negotiator.acceptNegotiator']
    ->negotiate($request);

$container['negotiator.contentTypeNegotiator']
    ->negotiate($request);
```

## Copyright

Dominik Zogg 2017

[1]: https://packagist.org/packages/chubbyphp/chubbyphp-negotiation
