<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Negotiation\Unit\Container;

use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use Chubbyphp\Negotiation\AcceptLanguageNegotiatorInterface;
use Chubbyphp\Negotiation\Container\AcceptLanguageNegotiatorFactory;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \Chubbyphp\Negotiation\Container\AcceptLanguageNegotiatorFactory
 *
 * @internal
 */
final class AcceptLanguageNegotiatorFactoryTest extends TestCase
{
    use MockByCallsTrait;

    public function testInvoke(): void
    {
        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')
                ->with(AcceptLanguageNegotiatorInterface::class.'supportedLocales[]')
                ->willReturn(['de-CH']),
        ]);

        $factory = new AcceptLanguageNegotiatorFactory();

        $negotiator = $factory($container);

        self::assertInstanceOf(AcceptLanguageNegotiatorInterface::class, $negotiator);
    }
}
