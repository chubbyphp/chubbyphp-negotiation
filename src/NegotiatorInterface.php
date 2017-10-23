<?php

declare(strict_types=1);

namespace Chubbyphp\Negotiation;

use Psr\Http\Message\ServerRequestInterface as Request;

interface NegotiatorInterface
{
    /**
     * @param Request $request
     *
     * @return NegotiatedValueInterface|null
     */
    public function negotiate(Request $request);
}
