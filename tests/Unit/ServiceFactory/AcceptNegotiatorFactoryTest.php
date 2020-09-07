<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Negotiation\Unit\ServiceFactory;

use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use Chubbyphp\Negotiation\AcceptNegotiatorInterface;
use Chubbyphp\Negotiation\ServiceFactory\AcceptNegotiatorFactory;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \Chubbyphp\Negotiation\ServiceFactory\AcceptNegotiatorFactory
 *
 * @internal
 */
final class AcceptNegotiatorFactoryTest extends TestCase
{
    use MockByCallsTrait;

    public function testInvoke(): void
    {
        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with(AcceptNegotiatorInterface::class.'supportedMediaTypes[]')->willReturn([]),
        ]);

        $factory = new AcceptNegotiatorFactory();

        $service = $factory($container);

        self::assertInstanceOf(AcceptNegotiatorInterface::class, $service);
    }

    public function testCallStatic(): void
    {
        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with(AcceptNegotiatorInterface::class.'supportedMediaTypes[]default')->willReturn([]),
        ]);

        $factory = [AcceptNegotiatorFactory::class, 'default'];

        $service = $factory($container);

        self::assertInstanceOf(AcceptNegotiatorInterface::class, $service);
    }
}
