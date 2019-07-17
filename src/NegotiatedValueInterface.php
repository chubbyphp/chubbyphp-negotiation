<?php

declare(strict_types=1);

namespace Chubbyphp\Negotiation;

interface NegotiatedValueInterface
{
    /**
     * @return string
     */
    public function getValue(): string;

    /**
     * @return array
     */
    public function getAttributes(): array;
}
