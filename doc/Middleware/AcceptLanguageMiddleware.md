# AcceptLanguageMiddleware

A [PSR-15][1] middleware around the [AcceptLanguageNegotiator](../AcceptLanguageNegotiator.md). It requires
`psr/http-server-middleware` and `chubbyphp/chubbyphp-http-exception`.

On success the negotiated value is stored in the request attribute `acceptLanguage` and the next handler is called.
On failure a `Chubbyphp\HttpException\HttpException` with status `406 Not Acceptable` is thrown.

```php
<?php

use Chubbyphp\Negotiation\AcceptLanguageNegotiator;
use Chubbyphp\Negotiation\Middleware\AcceptLanguageMiddleware;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/** @var ServerRequestInterface $request */
/** @var RequestHandlerInterface $handler */
$request = $request->withHeader('Accept-Language', 'de,en-US;q=0.7,en;q=0.3');

$middleware = new AcceptLanguageMiddleware(new AcceptLanguageNegotiator(['en', 'de']));

$response = $middleware->process($request, $handler);

// inside $handler: $request->getAttribute('acceptLanguage') === 'de'
```

## Error handling

When the negotiator returns `null` (missing header or no supported match) the middleware throws
`HttpException::createNotAcceptable()` carrying the following data, which your error handler can render:

```php
[
    'detail' => 'Not supported acceptLanguage, supportedValues: "en", de"', // 'Missing acceptLanguage, ...' if the header is absent
    'value' => '<the raw header value>',
    'supportedValues' => ['en', 'de'],
]
```

[1]: https://www.php-fig.org/psr/psr-15/
