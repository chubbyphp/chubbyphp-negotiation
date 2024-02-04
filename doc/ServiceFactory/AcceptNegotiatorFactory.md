# AcceptNegotiatorFactory

## without name (default)

```php
<?php

use Chubbyphp\Negotiation\AcceptNegotiatorInterface;
use Chubbyphp\Negotiation\ServiceFactory\AcceptNegotiatorFactory;
use Psr\Container\ContainerInterface;

/** @var ContainerInterface $container */
$container = ...;

// $container->get(AcceptNegotiatorInterface::class.'supportedMediaTypes[]')

$factory = new AcceptNegotiatorFactory();

$acceptNegotiator = $factory($container);
```

## with name `default`

```php
<?php

use Chubbyphp\Negotiation\AcceptNegotiatorInterface;
use Chubbyphp\Negotiation\ServiceFactory\AcceptNegotiatorFactory;
use Psr\Container\ContainerInterface;

/** @var ContainerInterface $container */
$container = ...;

// $container->get(AcceptNegotiatorInterface::class.'supportedMediaTypes[]default')

$factory = [AcceptNegotiatorFactory::class, 'default'];

$acceptNegotiator = $factory($container);
```
