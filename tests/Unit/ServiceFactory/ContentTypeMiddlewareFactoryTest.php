<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Negotiation\Unit\ServiceFactory;

use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockObjectBuilder;
use Chubbyphp\Negotiation\ContentTypeNegotiatorInterface;
use Chubbyphp\Negotiation\ServiceFactory\ContentTypeMiddlewareFactory;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;

/**
 * @covers \Chubbyphp\Negotiation\ServiceFactory\ContentTypeMiddlewareFactory
 *
 * @internal
 */
final class ContentTypeMiddlewareFactoryTest extends TestCase
{
    public function testInvoke(): void
    {
        $builder = new MockObjectBuilder();

        /** @var ContentTypeNegotiatorInterface $contentTypeNegotiator */
        $contentTypeNegotiator = $builder->create(ContentTypeNegotiatorInterface::class, []);

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('get', [ContentTypeNegotiatorInterface::class], $contentTypeNegotiator),
        ]);

        $factory = new ContentTypeMiddlewareFactory();

        $service = $factory($container);

        self::assertInstanceOf(MiddlewareInterface::class, $service);
    }

    public function testCallStatic(): void
    {
        $builder = new MockObjectBuilder();

        /** @var ContentTypeNegotiatorInterface $contentTypeNegotiator */
        $contentTypeNegotiator = $builder->create(ContentTypeNegotiatorInterface::class, []);

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('get', [ContentTypeNegotiatorInterface::class.'default'], $contentTypeNegotiator),
        ]);

        $factory = [ContentTypeMiddlewareFactory::class, 'default'];

        $service = $factory($container);

        self::assertInstanceOf(MiddlewareInterface::class, $service);
    }
}
