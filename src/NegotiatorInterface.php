<?php

declare(strict_types=1);

namespace Chubbyphp\Negotiation;

use Psr\Http\Message\ServerRequestInterface as Request;

interface NegotiatorInterface
{
    public function negotiate(Request $request): ?NegotiatedValueInterface;
}
