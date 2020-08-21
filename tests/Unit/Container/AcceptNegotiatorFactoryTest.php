<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Negotiation\Unit\Container;

use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use Chubbyphp\Negotiation\AcceptNegotiatorInterface;
use Chubbyphp\Negotiation\Container\AcceptNegotiatorFactory;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \Chubbyphp\Negotiation\Container\AcceptNegotiatorFactory
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
            Call::create('get')
                ->with(AcceptNegotiatorInterface::class.'supportedMediaTypes[]')
                ->willReturn(['application/json']),
        ]);

        $factory = new AcceptNegotiatorFactory();

        $negotiator = $factory($container);

        self::assertInstanceOf(AcceptNegotiatorInterface::class, $negotiator);
    }
}
