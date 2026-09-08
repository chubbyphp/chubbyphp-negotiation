# ContentTypeNegotiator

Negotiates the `Content-Type` request header ([RFC 7231 §3.1.1.5][1]) against a list of supported media types.
Use it to decide whether a request body can be parsed before reading it.

```php
<?php

use Chubbyphp\Negotiation\ContentTypeNegotiator;
use Psr\Http\Message\ServerRequestInterface;

/** @var ServerRequestInterface $request */
$request = $request->withHeader('Content-Type', 'application/xml; charset=UTF-8');

$negotiator = new ContentTypeNegotiator(['application/json', 'application/xml', 'application/x-yaml']);

$value = $negotiator->negotiate($request); // NegotiatedValue|null
$value->getValue();                        // 'application/xml'
$value->getAttributes();                   // ['charset' => 'UTF-8']

$negotiator->getSupportedMediaTypes();     // ['application/json', 'application/xml', 'application/x-yaml']
```

## Matching rules

Unlike `Accept`, a `Content-Type` header carries exactly one media type, so there is no `q` ordering.
Parameters such as `charset` or `boundary` are returned as attributes.

| # | Rule                                  | Header                                | Supported             | Result                              |
|---|---------------------------------------|---------------------------------------|-----------------------|-------------------------------------|
| 1 | exact media type                      | `application/xml; charset=UTF-8`      | `['application/xml']` | `application/xml` (`charset=UTF-8`) |
| 2 | structured suffix (`type/sub+suffix`) | `application/vnd.api+json`            | `['application/json']`| `application/json`                  |

Rule 2 works from the **request** side: a request body declared as `application/vnd.api+json`
is accepted by a parser that supports `application/json`.

## Returns `null` when

 * the list of supported media types is empty
 * the request has no `Content-Type` header
 * the header contains a comma (multiple media types are not valid for `Content-Type`)
 * no rule above produces a match

[1]: https://www.rfc-editor.org/rfc/rfc7231#section-3.1.1.5
