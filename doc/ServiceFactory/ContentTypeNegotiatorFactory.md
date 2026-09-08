# ContentTypeNegotiatorFactory

Creates a `ContentTypeNegotiator`. The supported values are read from the container under the id `ContentTypeNegotiatorInterface::class.'supportedMediaTypes[]'` (suffixed with the factory name, if any), so register a `list<string>` under that id first.

The factory can be used unnamed (default) or with a name. A name is appended to every container id the factory
reads, which lets you register several independent instances side by side.

## without name (default)

```php
<?php

use Chubbyphp\Negotiation\ContentTypeNegotiatorInterface;
use Chubbyphp\Negotiation\ContentTypeNegotiator;
use Chubbyphp\Negotiation\ServiceFactory\ContentTypeNegotiatorFactory;
use Psr\Container\ContainerInterface;

/** @var ContainerInterface $container */
$container = ...;

// requires: $container->get(ContentTypeNegotiatorInterface::class.'supportedMediaTypes[]') // e.g. ['application/json', 'application/xml']

$factory = new ContentTypeNegotiatorFactory();

$contentTypeNegotiator = $factory($container); // ContentTypeNegotiator
```

## with name `default`

```php
<?php

use Chubbyphp\Negotiation\ContentTypeNegotiatorInterface;
use Chubbyphp\Negotiation\ContentTypeNegotiator;
use Chubbyphp\Negotiation\ServiceFactory\ContentTypeNegotiatorFactory;
use Psr\Container\ContainerInterface;

/** @var ContainerInterface $container */
$container = ...;

// requires: $container->get(ContentTypeNegotiatorInterface::class.'supportedMediaTypes[]default') // e.g. ['application/json', 'application/xml']

$factory = [ContentTypeNegotiatorFactory::class, 'default'];

$contentTypeNegotiator = $factory($container); // ContentTypeNegotiator
```
