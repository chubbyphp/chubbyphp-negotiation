# AcceptMiddlewareFactory

Creates an `AcceptMiddleware` (PSR-15 middleware). It resolves its negotiator from the container under the id `AcceptNegotiatorInterface::class` (suffixed with the factory name, if any), so register a negotiator, for example via [AcceptNegotiatorFactory](AcceptNegotiatorFactory.md), under that id first.

The factory can be used unnamed (default) or with a name. A name is appended to every container id the factory
reads, which lets you register several independent instances side by side.

## without name (default)

```php
<?php

use Chubbyphp\Negotiation\AcceptNegotiatorInterface;
use Chubbyphp\Negotiation\Middleware\AcceptMiddleware;
use Chubbyphp\Negotiation\ServiceFactory\AcceptMiddlewareFactory;
use Psr\Container\ContainerInterface;

/** @var ContainerInterface $container */
$container = ...;

// requires: $container->get(AcceptNegotiatorInterface::class)

$factory = new AcceptMiddlewareFactory();

$acceptMiddleware = $factory($container); // AcceptMiddleware
```

## with name `default`

```php
<?php

use Chubbyphp\Negotiation\AcceptNegotiatorInterface;
use Chubbyphp\Negotiation\Middleware\AcceptMiddleware;
use Chubbyphp\Negotiation\ServiceFactory\AcceptMiddlewareFactory;
use Psr\Container\ContainerInterface;

/** @var ContainerInterface $container */
$container = ...;

// requires: $container->get(AcceptNegotiatorInterface::class.'default')

$factory = [AcceptMiddlewareFactory::class, 'default'];

$acceptMiddleware = $factory($container); // AcceptMiddleware
```
