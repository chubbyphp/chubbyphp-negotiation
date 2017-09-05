<?php

declare(strict_types=1);

namespace Chubbyphp\Negotiation;

/**
 * @see https://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.4
 */
interface AcceptNegotiatorInterface extends NegotiatorInterface
{
    /**
     * @return string[]
     */
    public function getSupportedMediaTypes(): array;
}
