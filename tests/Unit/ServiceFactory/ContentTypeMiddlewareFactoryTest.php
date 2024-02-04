<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Negotiation\Unit\ServiceFactory;

use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
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
    use MockByCallsTrait;

    public function testInvoke(): void
    {
        /** @var ContentTypeNegotiatorInterface $contentTypeNegotiator */
        $contentTypeNegotiator = $this->getMockByCalls(ContentTypeNegotiatorInterface::class, []);

        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with(ContentTypeNegotiatorInterface::class)->willReturn($contentTypeNegotiator),
        ]);

        $factory = new ContentTypeMiddlewareFactory();

        $service = $factory($container);

        self::assertInstanceOf(MiddlewareInterface::class, $service);
    }

    public function testCallStatic(): void
    {
        /** @var ContentTypeNegotiatorInterface $contentTypeNegotiator */
        $contentTypeNegotiator = $this->getMockByCalls(ContentTypeNegotiatorInterface::class, []);

        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with(ContentTypeNegotiatorInterface::class.'default')->willReturn($contentTypeNegotiator),
        ]);

        $factory = [ContentTypeMiddlewareFactory::class, 'default'];

        $service = $factory($container);

        self::assertInstanceOf(MiddlewareInterface::class, $service);
    }
}
