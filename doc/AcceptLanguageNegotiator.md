# AcceptLanguageNegotiator

Negotiates the `Accept-Language` request header ([RFC 7231 §5.3.5][1]) against a list of supported locales.

```php
<?php

use Chubbyphp\Negotiation\AcceptLanguageNegotiator;
use Psr\Http\Message\ServerRequestInterface;

/** @var ServerRequestInterface $request */
$request = $request->withHeader('Accept-Language', 'de,en-US;q=0.7,en;q=0.3');

$negotiator = new AcceptLanguageNegotiator(['en', 'de']);

$value = $negotiator->negotiate($request); // NegotiatedValue|null
$value->getValue();                        // 'de'
$value->getAttributes();                   // ['q' => '1.0']

$negotiator->getSupportedLocales();        // ['en', 'de']
```

## Matching rules

The header is split into locales, each with its parameters (a missing `q` counts as `1.0`),
and sorted by `q` descending. The first rule that produces a match wins:

| # | Rule                                  | Header (sorted by `q`) | Supported      | Result             |
|---|---------------------------------------|------------------------|----------------|--------------------|
| 1 | exact locale                          | `de,en;q=0.3`          | `['en', 'de']` | `de` (`q=1.0`)     |
| 2 | language of a regional locale         | `de-CH`                | `['de']`       | `de` (`q=1.0`)     |
| 3 | wildcard `*` → first supported locale | `en-US;q=0.7,*;q=0.3`  | `['de']`       | `de` (`q=0.3`)     |

Rule 2 only strips a single region (`de-CH` → `de`); `de-DE-AT` does not match `de`.
The fallback is one-directional: a requested `de` does **not** match a supported `de-CH`.

## Returns `null` when

 * the list of supported locales is empty
 * the request has no `Accept-Language` header
 * no rule above produces a match

[1]: https://www.rfc-editor.org/rfc/rfc7231#section-5.3.5
