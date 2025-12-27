<?php

declare(strict_types=1);

namespace Chubbyphp\Negotiation;

final class NegotiatedValue implements NegotiatedValueInterface
{
    /**
     * @param array<string, string> $attributes
     */
    public function __construct(private readonly string $value, private readonly array $attributes = []) {}

    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @return array<string, string>
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }
}
