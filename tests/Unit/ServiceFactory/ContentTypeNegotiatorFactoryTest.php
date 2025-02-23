<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Negotiation\Unit\ServiceFactory;

use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockObjectBuilder;
use Chubbyphp\Negotiation\ContentTypeNegotiatorInterface;
use Chubbyphp\Negotiation\ServiceFactory\ContentTypeNegotiatorFactory;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \Chubbyphp\Negotiation\ServiceFactory\ContentTypeNegotiatorFactory
 *
 * @internal
 */
final class ContentTypeNegotiatorFactoryTest extends TestCase
{
    public function testInvoke(): void
    {
        $builder = new MockObjectBuilder();

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('get', [ContentTypeNegotiatorInterface::class.'supportedMediaTypes[]'], []),
        ]);

        $factory = new ContentTypeNegotiatorFactory();

        $service = $factory($container);

        self::assertInstanceOf(ContentTypeNegotiatorInterface::class, $service);
    }

    public function testCallStatic(): void
    {
        $builder = new MockObjectBuilder();

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('get', [ContentTypeNegotiatorInterface::class.'supportedMediaTypes[]default'], []),
        ]);

        $factory = [ContentTypeNegotiatorFactory::class, 'default'];

        $service = $factory($container);

        self::assertInstanceOf(ContentTypeNegotiatorInterface::class, $service);
    }
}
