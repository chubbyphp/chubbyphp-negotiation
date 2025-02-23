<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Negotiation\Unit\ServiceFactory;

use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockObjectBuilder;
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
    public function testInvoke(): void
    {
        $builder = new MockObjectBuilder();

        /** @var AcceptNegotiatorInterface $acceptNegotiator */
        $acceptNegotiator = $builder->create(AcceptNegotiatorInterface::class, []);

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('get', [AcceptNegotiatorInterface::class], $acceptNegotiator),
        ]);

        $factory = new AcceptMiddlewareFactory();

        $service = $factory($container);

        self::assertInstanceOf(MiddlewareInterface::class, $service);
    }

    public function testCallStatic(): void
    {
        $builder = new MockObjectBuilder();

        /** @var AcceptNegotiatorInterface $acceptNegotiator */
        $acceptNegotiator = $builder->create(AcceptNegotiatorInterface::class, []);

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('get', [AcceptNegotiatorInterface::class.'default'], $acceptNegotiator),
        ]);

        $factory = [AcceptMiddlewareFactory::class, 'default'];

        $service = $factory($container);

        self::assertInstanceOf(MiddlewareInterface::class, $service);
    }
}
