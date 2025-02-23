<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Negotiation\Unit\ServiceFactory;

use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockObjectBuilder;
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
    public function testInvoke(): void
    {
        $builder = new MockObjectBuilder();

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('get', [AcceptNegotiatorInterface::class.'supportedMediaTypes[]'], []),
        ]);

        $factory = new AcceptNegotiatorFactory();

        $service = $factory($container);

        self::assertInstanceOf(AcceptNegotiatorInterface::class, $service);
    }

    public function testCallStatic(): void
    {
        $builder = new MockObjectBuilder();

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('get', [AcceptNegotiatorInterface::class.'supportedMediaTypes[]default'], []),
        ]);

        $factory = [AcceptNegotiatorFactory::class, 'default'];

        $service = $factory($container);

        self::assertInstanceOf(AcceptNegotiatorInterface::class, $service);
    }
}
