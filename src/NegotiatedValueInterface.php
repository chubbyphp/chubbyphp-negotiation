<?php

declare(strict_types=1);

namespace Chubbyphp\Negotiation;

interface NegotiatedValueInterface
{
    public function getValue(): string;

    /**
     * @return array<string, string>
     */
    public function getAttributes(): array;
}
