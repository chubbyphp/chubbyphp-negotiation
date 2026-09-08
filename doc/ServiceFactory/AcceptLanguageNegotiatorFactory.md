# AcceptLanguageNegotiatorFactory

Creates an `AcceptLanguageNegotiator`. The supported values are read from the container under the id `AcceptLanguageNegotiatorInterface::class.'supportedLocales[]'` (suffixed with the factory name, if any), so register a `list<string>` under that id first.

The factory can be used unnamed (default) or with a name. A name is appended to every container id the factory
reads, which lets you register several independent instances side by side.

## without name (default)

```php
<?php

use Chubbyphp\Negotiation\AcceptLanguageNegotiatorInterface;
use Chubbyphp\Negotiation\AcceptLanguageNegotiator;
use Chubbyphp\Negotiation\ServiceFactory\AcceptLanguageNegotiatorFactory;
use Psr\Container\ContainerInterface;

/** @var ContainerInterface $container */
$container = ...;

// requires: $container->get(AcceptLanguageNegotiatorInterface::class.'supportedLocales[]') // e.g. ['en', 'de']

$factory = new AcceptLanguageNegotiatorFactory();

$acceptLanguageNegotiator = $factory($container); // AcceptLanguageNegotiator
```

## with name `default`

```php
<?php

use Chubbyphp\Negotiation\AcceptLanguageNegotiatorInterface;
use Chubbyphp\Negotiation\AcceptLanguageNegotiator;
use Chubbyphp\Negotiation\ServiceFactory\AcceptLanguageNegotiatorFactory;
use Psr\Container\ContainerInterface;

/** @var ContainerInterface $container */
$container = ...;

// requires: $container->get(AcceptLanguageNegotiatorInterface::class.'supportedLocales[]default') // e.g. ['en', 'de']

$factory = [AcceptLanguageNegotiatorFactory::class, 'default'];

$acceptLanguageNegotiator = $factory($container); // AcceptLanguageNegotiator
```
