<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Negotiation\Unit\ServiceFactory;

use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use Chubbyphp\Negotiation\AcceptLanguageNegotiatorInterface;
use Chubbyphp\Negotiation\ServiceFactory\AcceptLanguageMiddlewareFactory;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;

/**
 * @covers \Chubbyphp\Negotiation\ServiceFactory\AcceptLanguageMiddlewareFactory
 *
 * @internal
 */
final class AcceptLanguageMiddlewareFactoryTest extends TestCase
{
    use MockByCallsTrait;

    public function testInvoke(): void
    {
        /** @var AcceptLanguageNegotiatorInterface $acceptLanguageNegotiator */
        $acceptLanguageNegotiator = $this->getMockByCalls(AcceptLanguageNegotiatorInterface::class, []);

        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with(AcceptLanguageNegotiatorInterface::class)->willReturn($acceptLanguageNegotiator),
        ]);

        $factory = new AcceptLanguageMiddlewareFactory();

        $service = $factory($container);

        self::assertInstanceOf(MiddlewareInterface::class, $service);
    }

    public function testCallStatic(): void
    {
        /** @var AcceptLanguageNegotiatorInterface $acceptLanguageNegotiator */
        $acceptLanguageNegotiator = $this->getMockByCalls(AcceptLanguageNegotiatorInterface::class, []);

        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with(AcceptLanguageNegotiatorInterface::class.'default')->willReturn($acceptLanguageNegotiator),
        ]);

        $factory = [AcceptLanguageMiddlewareFactory::class, 'default'];

        $service = $factory($container);

        self::assertInstanceOf(MiddlewareInterface::class, $service);
    }
}
