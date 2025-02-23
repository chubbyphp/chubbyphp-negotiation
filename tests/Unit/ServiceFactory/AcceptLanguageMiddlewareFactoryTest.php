<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Negotiation\Unit\ServiceFactory;

use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockObjectBuilder;
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
    public function testInvoke(): void
    {
        $builder = new MockObjectBuilder();

        /** @var AcceptLanguageNegotiatorInterface $acceptLanguageNegotiator */
        $acceptLanguageNegotiator = $builder->create(AcceptLanguageNegotiatorInterface::class, []);

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('get', [AcceptLanguageNegotiatorInterface::class], $acceptLanguageNegotiator),
        ]);

        $factory = new AcceptLanguageMiddlewareFactory();

        $service = $factory($container);

        self::assertInstanceOf(MiddlewareInterface::class, $service);
    }

    public function testCallStatic(): void
    {
        $builder = new MockObjectBuilder();

        /** @var AcceptLanguageNegotiatorInterface $acceptLanguageNegotiator */
        $acceptLanguageNegotiator = $builder->create(AcceptLanguageNegotiatorInterface::class, []);

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('get', [AcceptLanguageNegotiatorInterface::class.'default'], $acceptLanguageNegotiator),
        ]);

        $factory = [AcceptLanguageMiddlewareFactory::class, 'default'];

        $service = $factory($container);

        self::assertInstanceOf(MiddlewareInterface::class, $service);
    }
}
