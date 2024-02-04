# AcceptLanguageNegotiatorFactory

## without name (default)

```php
<?php

use Chubbyphp\Negotiation\AcceptLanguageNegotiatorInterface;
use Chubbyphp\Negotiation\ServiceFactory\AcceptLanguageNegotiatorFactory;
use Psr\Container\ContainerInterface;

/** @var ContainerInterface $container */
$container = ...;

// $container->get(AcceptLanguageNegotiatorInterface::class.'supportedLocales[]')

$factory = new AcceptLanguageNegotiatorFactory();

$acceptLanguageNegotiator = $factory($container);
```

## with name `default`

```php
<?php

use Chubbyphp\Negotiation\AcceptLanguageNegotiatorInterface;
use Chubbyphp\Negotiation\ServiceFactory\AcceptLanguageNegotiatorFactory;
use Psr\Container\ContainerInterface;

/** @var ContainerInterface $container */
$container = ...;

// $container->get(AcceptLanguageNegotiatorInterface::class.'supportedLocales[]default')

$factory = [AcceptLanguageNegotiatorFactory::class, 'default'];

$acceptLanguageNegotiator = $factory($container);
```
