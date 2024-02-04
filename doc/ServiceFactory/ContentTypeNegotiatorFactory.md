# ContentTypeNegotiatorFactory

## without name (default)

```php
<?php

use Chubbyphp\Negotiation\ContentTypeNegotiatorInterface;
use Chubbyphp\Negotiation\ServiceFactory\ContentTypeNegotiatorFactory;
use Psr\Container\ContainerInterface;

/** @var ContainerInterface $container */
$container = ...;

// $container->get(ContentTypeNegotiatorInterface::class.'supportedMediaTypes[]')

$factory = new ContentTypeNegotiatorFactory();

$contentTypeNegotiator = $factory($container);
```

## with name `default`

```php
<?php

use Chubbyphp\Negotiation\ContentTypeNegotiatorInterface;
use Chubbyphp\Negotiation\ServiceFactory\ContentTypeNegotiatorFactory;
use Psr\Container\ContainerInterface;

/** @var ContainerInterface $container */
$container = ...;

// $container->get(ContentTypeNegotiatorInterface::class.'supportedMediaTypes[]default')

$factory = [ContentTypeNegotiatorFactory::class, 'default'];

$contentTypeNegotiator = $factory($container);
```
