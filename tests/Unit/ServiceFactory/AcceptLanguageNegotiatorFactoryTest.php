<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Negotiation\Unit\ServiceFactory;

use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockObjectBuilder;
use Chubbyphp\Negotiation\AcceptLanguageNegotiatorInterface;
use Chubbyphp\Negotiation\ServiceFactory\AcceptLanguageNegotiatorFactory;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \Chubbyphp\Negotiation\ServiceFactory\AcceptLanguageNegotiatorFactory
 *
 * @internal
 */
final class AcceptLanguageNegotiatorFactoryTest extends TestCase
{
    public function testInvoke(): void
    {
        $builder = new MockObjectBuilder();

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('get', [AcceptLanguageNegotiatorInterface::class.'supportedLocales[]'], []),
        ]);

        $factory = new AcceptLanguageNegotiatorFactory();

        $service = $factory($container);

        self::assertInstanceOf(AcceptLanguageNegotiatorInterface::class, $service);
    }

    public function testCallStatic(): void
    {
        $builder = new MockObjectBuilder();

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('get', [AcceptLanguageNegotiatorInterface::class.'supportedLocales[]default'], []),
        ]);

        $factory = [AcceptLanguageNegotiatorFactory::class, 'default'];

        $service = $factory($container);

        self::assertInstanceOf(AcceptLanguageNegotiatorInterface::class, $service);
    }
}
