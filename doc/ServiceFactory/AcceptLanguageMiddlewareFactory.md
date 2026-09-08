# AcceptLanguageMiddlewareFactory

Creates an `AcceptLanguageMiddleware` (PSR-15 middleware). It resolves its negotiator from the container under the id `AcceptLanguageNegotiatorInterface::class` (suffixed with the factory name, if any), so register a negotiator, for example via [AcceptLanguageNegotiatorFactory](AcceptLanguageNegotiatorFactory.md), under that id first.

The factory can be used unnamed (default) or with a name. A name is appended to every container id the factory
reads, which lets you register several independent instances side by side.

## without name (default)

```php
<?php

use Chubbyphp\Negotiation\AcceptLanguageNegotiatorInterface;
use Chubbyphp\Negotiation\Middleware\AcceptLanguageMiddleware;
use Chubbyphp\Negotiation\ServiceFactory\AcceptLanguageMiddlewareFactory;
use Psr\Container\ContainerInterface;

/** @var ContainerInterface $container */
$container = ...;

// requires: $container->get(AcceptLanguageNegotiatorInterface::class)

$factory = new AcceptLanguageMiddlewareFactory();

$acceptLanguageMiddleware = $factory($container); // AcceptLanguageMiddleware
```

## with name `default`

```php
<?php

use Chubbyphp\Negotiation\AcceptLanguageNegotiatorInterface;
use Chubbyphp\Negotiation\Middleware\AcceptLanguageMiddleware;
use Chubbyphp\Negotiation\ServiceFactory\AcceptLanguageMiddlewareFactory;
use Psr\Container\ContainerInterface;

/** @var ContainerInterface $container */
$container = ...;

// requires: $container->get(AcceptLanguageNegotiatorInterface::class.'default')

$factory = [AcceptLanguageMiddlewareFactory::class, 'default'];

$acceptLanguageMiddleware = $factory($container); // AcceptLanguageMiddleware
```
