# ContentTypeMiddlewareFactory

Creates a `ContentTypeMiddleware` (PSR-15 middleware). It resolves its negotiator from the container under the id `ContentTypeNegotiatorInterface::class` (suffixed with the factory name, if any), so register a negotiator, for example via [ContentTypeNegotiatorFactory](ContentTypeNegotiatorFactory.md), under that id first.

The factory can be used unnamed (default) or with a name. A name is appended to every container id the factory
reads, which lets you register several independent instances side by side.

## without name (default)

```php
<?php

use Chubbyphp\Negotiation\ContentTypeNegotiatorInterface;
use Chubbyphp\Negotiation\Middleware\ContentTypeMiddleware;
use Chubbyphp\Negotiation\ServiceFactory\ContentTypeMiddlewareFactory;
use Psr\Container\ContainerInterface;

/** @var ContainerInterface $container */
$container = ...;

// requires: $container->get(ContentTypeNegotiatorInterface::class)

$factory = new ContentTypeMiddlewareFactory();

$contentTypeMiddleware = $factory($container); // ContentTypeMiddleware
```

## with name `default`

```php
<?php

use Chubbyphp\Negotiation\ContentTypeNegotiatorInterface;
use Chubbyphp\Negotiation\Middleware\ContentTypeMiddleware;
use Chubbyphp\Negotiation\ServiceFactory\ContentTypeMiddlewareFactory;
use Psr\Container\ContainerInterface;

/** @var ContainerInterface $container */
$container = ...;

// requires: $container->get(ContentTypeNegotiatorInterface::class.'default')

$factory = [ContentTypeMiddlewareFactory::class, 'default'];

$contentTypeMiddleware = $factory($container); // ContentTypeMiddleware
```
