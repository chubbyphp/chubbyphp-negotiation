<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Negotiation\Unit\Container;

use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use Chubbyphp\Negotiation\Container\ContentTypeNegotiatorFactory;
use Chubbyphp\Negotiation\ContentTypeNegotiatorInterface;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \Chubbyphp\Negotiation\Container\ContentTypeNegotiatorFactory
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
            Call::create('get')
                ->with(ContentTypeNegotiatorInterface::class.'supportedMediaTypes[]')
                ->willReturn(['application/json']),
        ]);

        $factory = new ContentTypeNegotiatorFactory();

        $negotiator = $factory($container);

        self::assertInstanceOf(ContentTypeNegotiatorInterface::class, $negotiator);
    }
}
