# AcceptMiddlewareFactory

## without name (default)

```php
<?php

use Chubbyphp\Negotiation\AcceptMiddleware;
use Chubbyphp\Negotiation\ServiceFactory\AcceptMiddlewareFactory;
use Psr\Container\ContainerInterface;

/** @var ContainerInterface $container */
$container = ...;

// $container->get(AcceptNegotiator::class)

$factory = new AcceptMiddlewareFactory();

$acceptMiddleware = $factory($container);
```

## with name `default`

```php
<?php

use Chubbyphp\Negotiation\AcceptMiddleware;
use Chubbyphp\Negotiation\ServiceFactory\AcceptMiddlewareFactory;
use Psr\Container\ContainerInterface;

/** @var ContainerInterface $container */
$container = ...;

// $container->get(AcceptNegotiator::class.'default')

$factory = [AcceptMiddlewareFactory::class, 'default'];

$acceptMiddleware = $factory($container);
```
