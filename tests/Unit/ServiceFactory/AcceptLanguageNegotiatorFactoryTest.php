<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Negotiation\Unit\ServiceFactory;

use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
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
    use MockByCallsTrait;

    public function testInvoke(): void
    {
        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with(AcceptLanguageNegotiatorInterface::class.'supportedLocales[]')->willReturn([]),
        ]);

        $factory = new AcceptLanguageNegotiatorFactory();

        $service = $factory($container);

        self::assertInstanceOf(AcceptLanguageNegotiatorInterface::class, $service);
    }

    public function testCallStatic(): void
    {
        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with(AcceptLanguageNegotiatorInterface::class.'supportedLocales[]default')->willReturn([]),
        ]);

        $factory = [AcceptLanguageNegotiatorFactory::class, 'default'];

        $service = $factory($container);

        self::assertInstanceOf(AcceptLanguageNegotiatorInterface::class, $service);
    }
}
