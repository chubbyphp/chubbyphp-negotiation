# AcceptNegotiator

Negotiates the `Accept` request header ([RFC 7231 §5.3.2][1]) against a list of supported media types.

```php
<?php

use Chubbyphp\Negotiation\AcceptNegotiator;
use Psr\Http\Message\ServerRequestInterface;

/** @var ServerRequestInterface $request */
$request = $request->withHeader('Accept', 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8');

$negotiator = new AcceptNegotiator(['application/json', 'application/xml', 'application/x-yaml']);

$value = $negotiator->negotiate($request); // NegotiatedValue|null
$value->getValue();                        // 'application/xml'
$value->getAttributes();                   // ['q' => '0.9']

$negotiator->getSupportedMediaTypes();     // ['application/json', 'application/xml', 'application/x-yaml']
```

## Matching rules

The header is split into media types, each with its parameters (a missing `q` counts as `1.0`),
and sorted by `q` descending. The first rule that produces a match wins:

| # | Rule                                        | Header (sorted by `q`)                  | Supported                      | Result                             |
|---|---------------------------------------------|-----------------------------------------|--------------------------------|------------------------------------|
| 1 | exact media type                            | `text/html,application/xml;q=0.9`       | `['application/xml']`          | `application/xml` (`q=0.9`)        |
| 2 | structured suffix (`type/sub+suffix`)       | `application/json;q=0.9`                | `['application/vnd.api+json']` | `application/vnd.api+json` (`q=0.9`) |
| 3 | type wildcard (`type/*`)                    | `application/*`                         | `['application/json']`         | `application/json` (`q=1.0`)       |
| 4 | full wildcard `*/*` → first supported type  | `text/html,*/*;q=0.8`                   | `['application/json']`         | `application/json` (`q=0.8`)       |

Rule 2 works from the **supported** side: a supported `application/vnd.api+json` is also reachable as
`application/json`. The reverse (a requested `application/vnd.api+json` against a supported `application/json`)
is handled by the [ContentTypeNegotiator](ContentTypeNegotiator.md) semantics, not here.

## Returns `null` when

 * the list of supported media types is empty
 * the request has no `Accept` header
 * no rule above produces a match (for example `*/json`, which is not a valid media range)

[1]: https://www.rfc-editor.org/rfc/rfc7231#section-5.3.2
