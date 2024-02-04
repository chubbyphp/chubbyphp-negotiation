<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Negotiation\Unit\ServiceFactory;

use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use Chubbyphp\Negotiation\AcceptNegotiatorInterface;
use Chubbyphp\Negotiation\ServiceFactory\AcceptMiddlewareFactory;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;

/**
 * @covers \Chubbyphp\Negotiation\ServiceFactory\AcceptMiddlewareFactory
 *
 * @internal
 */
final class AcceptMiddlewareFactoryTest extends TestCase
{
    use MockByCallsTrait;

    public function testInvoke(): void
    {
        /** @var AcceptNegotiatorInterface $acceptNegotiator */
        $acceptNegotiator = $this->getMockByCalls(AcceptNegotiatorInterface::class, []);

        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with(AcceptNegotiatorInterface::class)->willReturn($acceptNegotiator),
        ]);

        $factory = new AcceptMiddlewareFactory();

        $service = $factory($container);

        self::assertInstanceOf(MiddlewareInterface::class, $service);
    }

    public function testCallStatic(): void
    {
        /** @var AcceptNegotiatorInterface $acceptNegotiator */
        $acceptNegotiator = $this->getMockByCalls(AcceptNegotiatorInterface::class, []);

        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with(AcceptNegotiatorInterface::class.'default')->willReturn($acceptNegotiator),
        ]);

        $factory = [AcceptMiddlewareFactory::class, 'default'];

        $service = $factory($container);

        self::assertInstanceOf(MiddlewareInterface::class, $service);
    }
}
