<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Negotiation;

use Chubbyphp\Negotiation\NegotiatedValue;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Negotiation\NegotiatedValue
 *
 * @internal
 */
final class NegotiatedValueTest extends TestCase
{
    public function testWithoutAttributes(): void
    {
        $negotiatedValue = new NegotiatedValue('application/json');

        self::assertSame('application/json', $negotiatedValue->getValue());
        self::assertEquals([], $negotiatedValue->getAttributes());
    }

    public function testWithAttributes(): void
    {
        $negotiatedValue = new NegotiatedValue('application/json', ['q' => '0.7']);

        self::assertSame('application/json', $negotiatedValue->getValue());
        self::assertEquals(['q' => '0.7'], $negotiatedValue->getAttributes());
    }
}
