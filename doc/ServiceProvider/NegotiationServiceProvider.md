# NegotiationServiceProvider

Registers all negotiators and middlewares in a [Pimple][1] container, using the same service ids as the
[NegotiationServiceFactory](../ServiceFactory/NegotiationServiceFactory.md). Requires `pimple/pimple`.

```php
<?php

use Chubbyphp\Negotiation\ServiceProvider\NegotiationServiceProvider;
use Pimple\Container;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

$container = new Container();
$container->register(new NegotiationServiceProvider());

// configure the supported values (default: [])
$container['negotiator.acceptNegotiator.values'] = ['application/json', 'application/xml'];
$container['negotiator.acceptLanguageNegotiator.values'] = ['en', 'de'];
$container['negotiator.contentTypeNegotiator.values'] = ['application/json', 'application/xml'];

/** @var ServerRequestInterface $request */
/** @var RequestHandlerInterface $handler */

$container['negotiator.acceptNegotiator']->negotiate($request);
$container['negotiator.acceptMiddleware']->process($request, $handler);

$container['negotiator.acceptLanguageNegotiator']->negotiate($request);
$container['negotiator.acceptLanguageMiddleware']->process($request, $handler);

$container['negotiator.contentTypeNegotiator']->negotiate($request);
$container['negotiator.contentTypeMiddleware']->process($request, $handler);
```

## Registered services

| Service id                                     | Type                       |
|------------------------------------------------|----------------------------|
| `negotiator.acceptNegotiator`                  | `AcceptNegotiator`         |
| `negotiator.acceptMiddleware`                  | `AcceptMiddleware`         |
| `negotiator.acceptLanguageNegotiator`          | `AcceptLanguageNegotiator` |
| `negotiator.acceptLanguageMiddleware`          | `AcceptLanguageMiddleware` |
| `negotiator.contentTypeNegotiator`             | `ContentTypeNegotiator`    |
| `negotiator.contentTypeMiddleware`             | `ContentTypeMiddleware`    |
| `negotiator.acceptNegotiator.values`           | `list<string>` (default `[]`) |
| `negotiator.acceptLanguageNegotiator.values`   | `list<string>` (default `[]`) |
| `negotiator.contentTypeNegotiator.values`      | `list<string>` (default `[]`) |

The `*.values` services hold the supported media types / locales. They default to empty lists,
which makes every negotiation fail, so override them with your own configuration.

[1]: https://github.com/silexphp/Pimple
