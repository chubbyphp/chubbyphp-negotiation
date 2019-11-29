<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Negotiation\Unit\ServiceProvider;

use Chubbyphp\Negotiation\AcceptLanguageNegotiator;
use Chubbyphp\Negotiation\AcceptNegotiator;
use Chubbyphp\Negotiation\ContentTypeNegotiator;
use Chubbyphp\Negotiation\ServiceProvider\NegotiationServiceProvider;
use PHPUnit\Framework\TestCase;
use Pimple\Container;

/**
 * @covers \Chubbyphp\Negotiation\ServiceProvider\NegotiationServiceProvider
 *
 * @internal
 */
final class NegotiationServiceProviderTest extends TestCase
{
    public function testRegister(): void
    {
        $container = new Container();
        $container->register(new NegotiationServiceProvider());

        self::assertTrue(isset($container['negotiator.acceptNegotiator']));
        self::assertTrue(isset($container['negotiator.acceptLanguageNegotiator']));
        self::assertTrue(isset($container['negotiator.contentTypeNegotiator']));
        self::assertTrue(isset($container['negotiator.acceptNegotiator.values']));
        self::assertTrue(isset($container['negotiator.acceptLanguageNegotiator.values']));
        self::assertTrue(isset($container['negotiator.contentTypeNegotiator.values']));

        self::assertInstanceOf(AcceptNegotiator::class, $container['negotiator.acceptNegotiator']);
        self::assertInstanceOf(AcceptLanguageNegotiator::class, $container['negotiator.acceptLanguageNegotiator']);
        self::assertInstanceOf(ContentTypeNegotiator::class, $container['negotiator.contentTypeNegotiator']);
        self::assertEquals([], $container['negotiator.acceptNegotiator.values']);
        self::assertEquals([], $container['negotiator.acceptLanguageNegotiator.values']);
        self::assertEquals([], $container['negotiator.contentTypeNegotiator.values']);
    }
}
