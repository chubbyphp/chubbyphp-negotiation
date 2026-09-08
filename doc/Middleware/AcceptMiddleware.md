# AcceptMiddleware

A [PSR-15][1] middleware around the [AcceptNegotiator](../AcceptNegotiator.md). It requires
`psr/http-server-middleware` and `chubbyphp/chubbyphp-http-exception`.

On success the negotiated value is stored in the request attribute `accept` and the next handler is called.
On failure a `Chubbyphp\HttpException\HttpException` with status `406 Not Acceptable` is thrown.

```php
<?php

use Chubbyphp\Negotiation\AcceptNegotiator;
use Chubbyphp\Negotiation\Middleware\AcceptMiddleware;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/** @var ServerRequestInterface $request */
/** @var RequestHandlerInterface $handler */
$request = $request->withHeader('Accept', 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8');

$middleware = new AcceptMiddleware(new AcceptNegotiator(['application/json', 'application/xml', 'application/x-yaml']));

$response = $middleware->process($request, $handler);

// inside $handler: $request->getAttribute('accept') === 'application/xml'
```

## Error handling

When the negotiator returns `null` (missing header or no supported match) the middleware throws
`HttpException::createNotAcceptable()` carrying the following data, which your error handler can render:

```php
[
    'detail' => 'Not supported accept, supportedValues: "application/json", application/xml", application/x-yaml"', // 'Missing accept, ...' if the header is absent
    'value' => '<the raw header value>',
    'supportedValues' => ['application/json', 'application/xml', 'application/x-yaml'],
]
```

[1]: https://www.php-fig.org/psr/psr-15/
