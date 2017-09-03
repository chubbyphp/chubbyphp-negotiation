<?php

namespace Chubbyphp\Tests\Negotiation;

use Chubbyphp\Negotiation\NegotiatedValue;

/**
 * @covers \Chubbyphp\Negotiation\NegotiatedValue
 */
final class NegotiatedValueTest extends \PHPUnit_Framework_TestCase
{
    public function testWithoutAttributes()
    {
        $negotiatedValue = new NegotiatedValue('application/json');

        self::assertSame('application/json', $negotiatedValue->getValue());
        self::assertEquals([], $negotiatedValue->getAttributes());
    }

    public function testWithAttributes()
    {
        $negotiatedValue = new NegotiatedValue('application/json', ['q' => '0.7']);

        self::assertSame('application/json', $negotiatedValue->getValue());
        self::assertEquals(['q' => '0.7'], $negotiatedValue->getAttributes());
    }
}
