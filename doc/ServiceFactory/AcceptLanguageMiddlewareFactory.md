# AcceptLanguageMiddlewareFactory

## without name (default)

```php
<?php

use Chubbyphp\Negotiation\AcceptLanguageMiddleware;
use Chubbyphp\Negotiation\ServiceFactory\AcceptLanguageMiddlewareFactory;
use Psr\Container\ContainerInterface;

/** @var ContainerInterface $container */
$container = ...;

// $container->get(AcceptLanguageNegotiator::class)

$factory = new AcceptLanguageMiddlewareFactory();

$acceptLanguageMiddleware = $factory($container);
```

## with name `default`

```php
<?php

use Chubbyphp\Negotiation\AcceptLanguageMiddleware;
use Chubbyphp\Negotiation\ServiceFactory\AcceptLanguageMiddlewareFactory;
use Psr\Container\ContainerInterface;

/** @var ContainerInterface $container */
$container = ...;

// $container->get(AcceptLanguageNegotiator::class.'default')

$factory = [AcceptLanguageMiddlewareFactory::class, 'default'];

$acceptLanguageMiddleware = $factory($container);
```
