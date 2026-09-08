# ContentTypeMiddleware

A [PSR-15][1] middleware around the [ContentTypeNegotiator](../ContentTypeNegotiator.md). It requires
`psr/http-server-middleware` and `chubbyphp/chubbyphp-http-exception`.

On success the negotiated value is stored in the request attribute `contentType` and the next handler is called.
On failure a `Chubbyphp\HttpException\HttpException` with status `415 Unsupported Media Type` is thrown.

```php
<?php

use Chubbyphp\Negotiation\ContentTypeNegotiator;
use Chubbyphp\Negotiation\Middleware\ContentTypeMiddleware;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/** @var ServerRequestInterface $request */
/** @var RequestHandlerInterface $handler */
$request = $request->withHeader('Content-Type', 'application/xml; charset=UTF-8');

$middleware = new ContentTypeMiddleware(new ContentTypeNegotiator(['application/json', 'application/xml', 'application/x-yaml']));

$response = $middleware->process($request, $handler);

// inside $handler: $request->getAttribute('contentType') === 'application/xml'
```

## Error handling

When the negotiator returns `null` (missing header or no supported match) the middleware throws
`HttpException::createUnsupportedMediaType()` carrying the following data, which your error handler can render:

```php
[
    'detail' => 'Not supported content-type, supportedValues: "application/json", application/xml", application/x-yaml"', // 'Missing content-type, ...' if the header is absent
    'value' => '<the raw header value>',
    'supportedValues' => ['application/json', 'application/xml', 'application/x-yaml'],
]
```

[1]: https://www.php-fig.org/psr/psr-15/
