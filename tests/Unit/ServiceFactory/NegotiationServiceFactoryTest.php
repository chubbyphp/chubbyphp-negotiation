<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Negotiation\Unit\ServiceFactory;

use Chubbyphp\Container\Container;
use Chubbyphp\Negotiation\AcceptLanguageNegotiator;
use Chubbyphp\Negotiation\AcceptNegotiator;
use Chubbyphp\Negotiation\ContentTypeNegotiator;
use Chubbyphp\Negotiation\Middleware\AcceptLanguageMiddleware;
use Chubbyphp\Negotiation\Middleware\AcceptMiddleware;
use Chubbyphp\Negotiation\Middleware\ContentTypeMiddleware;
use Chubbyphp\Negotiation\ServiceFactory\NegotiationServiceFactory;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Negotiation\ServiceFactory\NegotiationServiceFactory
 *
 * @internal
 */
final class NegotiationServiceFactoryTest extends TestCase
{
    public function testRegister(): void
    {
        $container = new Container();
        $container->factories((new NegotiationServiceFactory())());

        self::assertTrue($container->has('negotiator.acceptNegotiator'));
        self::assertTrue($container->has('negotiator.acceptMiddleware'));
        self::assertTrue($container->has('negotiator.acceptLanguageNegotiator'));
        self::assertTrue($container->has('negotiator.acceptLanguageMiddleware'));
        self::assertTrue($container->has('negotiator.contentTypeNegotiator'));
        self::assertTrue($container->has('negotiator.contentTypeMiddleware'));
        self::assertTrue($container->has('negotiator.acceptNegotiator.values'));
        self::assertTrue($container->has('negotiator.acceptLanguageNegotiator.values'));
        self::assertTrue($container->has('negotiator.contentTypeNegotiator.values'));

        self::assertInstanceOf(AcceptNegotiator::class, $container->get('negotiator.acceptNegotiator'));
        self::assertInstanceOf(AcceptMiddleware::class, $container->get('negotiator.acceptMiddleware'));
        self::assertInstanceOf(AcceptLanguageNegotiator::class, $container->get('negotiator.acceptLanguageNegotiator'));
        self::assertInstanceOf(AcceptLanguageMiddleware::class, $container->get('negotiator.acceptLanguageMiddleware'));
        self::assertInstanceOf(ContentTypeNegotiator::class, $container->get('negotiator.contentTypeNegotiator'));
        self::assertInstanceOf(ContentTypeMiddleware::class, $container->get('negotiator.contentTypeMiddleware'));
        self::assertEquals([], $container->get('negotiator.acceptNegotiator.values'));
        self::assertEquals([], $container->get('negotiator.acceptLanguageNegotiator.values'));
        self::assertEquals([], $container->get('negotiator.contentTypeNegotiator.values'));
    }
}
