<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Negotiation\Unit\ServiceFactory;

use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
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
    use MockByCallsTrait;

    public function testInvoke(): void
    {
        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with(ContentTypeNegotiatorInterface::class.'supportedMediaTypes[]')->willReturn([]),
        ]);

        $factory = new ContentTypeNegotiatorFactory();

        $service = $factory($container);

        self::assertInstanceOf(ContentTypeNegotiatorInterface::class, $service);
    }

    public function testCallStatic(): void
    {
        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with(ContentTypeNegotiatorInterface::class.'supportedMediaTypes[]default')->willReturn([]),
        ]);

        $factory = [ContentTypeNegotiatorFactory::class, 'default'];

        $service = $factory($container);

        self::assertInstanceOf(ContentTypeNegotiatorInterface::class, $service);
    }
}
