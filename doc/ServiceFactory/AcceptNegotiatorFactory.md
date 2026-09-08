# AcceptNegotiatorFactory

Creates an `AcceptNegotiator`. The supported values are read from the container under the id `AcceptNegotiatorInterface::class.'supportedMediaTypes[]'` (suffixed with the factory name, if any), so register a `list<string>` under that id first.

The factory can be used unnamed (default) or with a name. A name is appended to every container id the factory
reads, which lets you register several independent instances side by side.

## without name (default)

```php
<?php

use Chubbyphp\Negotiation\AcceptNegotiatorInterface;
use Chubbyphp\Negotiation\AcceptNegotiator;
use Chubbyphp\Negotiation\ServiceFactory\AcceptNegotiatorFactory;
use Psr\Container\ContainerInterface;

/** @var ContainerInterface $container */
$container = ...;

// requires: $container->get(AcceptNegotiatorInterface::class.'supportedMediaTypes[]') // e.g. ['application/json', 'application/xml']

$factory = new AcceptNegotiatorFactory();

$acceptNegotiator = $factory($container); // AcceptNegotiator
```

## with name `default`

```php
<?php

use Chubbyphp\Negotiation\AcceptNegotiatorInterface;
use Chubbyphp\Negotiation\AcceptNegotiator;
use Chubbyphp\Negotiation\ServiceFactory\AcceptNegotiatorFactory;
use Psr\Container\ContainerInterface;

/** @var ContainerInterface $container */
$container = ...;

// requires: $container->get(AcceptNegotiatorInterface::class.'supportedMediaTypes[]default') // e.g. ['application/json', 'application/xml']

$factory = [AcceptNegotiatorFactory::class, 'default'];

$acceptNegotiator = $factory($container); // AcceptNegotiator
```
