# ContentTypeMiddlewareFactory

## without name (default)

```php
<?php

use Chubbyphp\Negotiation\ContentTypeMiddleware;
use Chubbyphp\Negotiation\ServiceFactory\ContentTypeMiddlewareFactory;
use Psr\Container\ContainerInterface;

/** @var ContainerInterface $container */
$container = ...;

// $container->get(ContentTypeNegotiator::class)

$factory = new ContentTypeMiddlewareFactory();

$contentTypeMiddleware = $factory($container);
```

## with name `default`

```php
<?php

use Chubbyphp\Negotiation\ContentTypeMiddleware;
use Chubbyphp\Negotiation\ServiceFactory\ContentTypeMiddlewareFactory;
use Psr\Container\ContainerInterface;

/** @var ContainerInterface $container */
$container = ...;

// $container->get(ContentTypeNegotiator::class.'default')

$factory = [ContentTypeMiddlewareFactory::class, 'default'];

$contentTypeMiddleware = $factory($container);
```
