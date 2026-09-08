# NegotiationServiceFactory

Registers all negotiators and middlewares in a [chubbyphp/chubbyphp-container][1].
Requires `chubbyphp/chubbyphp-container`.

```php
<?php

use Chubbyphp\Container\Container;
use Chubbyphp\Negotiation\ServiceFactory\NegotiationServiceFactory;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

$container = new Container();
$container->factories((new NegotiationServiceFactory())());

// configure the supported values (default: [])
$container->factory('negotiator.acceptNegotiator.values', static fn (): array => ['application/json', 'application/xml']);
$container->factory('negotiator.acceptLanguageNegotiator.values', static fn (): array => ['en', 'de']);
$container->factory('negotiator.contentTypeNegotiator.values', static fn (): array => ['application/json', 'application/xml']);

/** @var ServerRequestInterface $request */
/** @var RequestHandlerInterface $handler */

$container->get('negotiator.acceptNegotiator')->negotiate($request);
$container->get('negotiator.acceptMiddleware')->process($request, $handler);

$container->get('negotiator.acceptLanguageNegotiator')->negotiate($request);
$container->get('negotiator.acceptLanguageMiddleware')->process($request, $handler);

$container->get('negotiator.contentTypeNegotiator')->negotiate($request);
$container->get('negotiator.contentTypeMiddleware')->process($request, $handler);
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

[1]: https://github.com/chubbyphp/chubbyphp-container
