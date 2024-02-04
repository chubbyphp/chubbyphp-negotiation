<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Negotiation\Unit\Middleware;

use Chubbyphp\HttpException\HttpException;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use Chubbyphp\Negotiation\AcceptLanguageNegotiatorInterface;
use Chubbyphp\Negotiation\Middleware\AcceptLanguageMiddleware;
use Chubbyphp\Negotiation\NegotiatedValueInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * @covers \Chubbyphp\Negotiation\Middleware\AcceptLanguageMiddleware
 *
 * @internal
 */
final class AcceptLanguageMiddlewareTest extends TestCase
{
    use MockByCallsTrait;

    public function testProcessWithoutMatching(): void
    {
        /** @var MockObject|ServerRequestInterface $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getHeaderLine')->with('Accept-Language')->willReturn('en-US, en;q=0.9'),
        ]);

        /** @var MockObject|RequestHandlerInterface $handler */
        $handler = $this->getMockByCalls(RequestHandlerInterface::class, []);

        /** @var AcceptLanguageNegotiatorInterface|MockObject $acceptLanguageNegotiator */
        $acceptLanguageNegotiator = $this->getMockByCalls(AcceptLanguageNegotiatorInterface::class, [
            Call::create('negotiate')->with($request)->willReturn(null),
            Call::create('getSupportedLocales')->with()->willReturn(['en-US']),
        ]);

        $middleware = new AcceptLanguageMiddleware($acceptLanguageNegotiator);

        try {
            $middleware->process($request, $handler);

            throw new \Exception('code should not be reached');
        } catch (HttpException $e) {
            self::assertSame('Not Acceptable', $e->getMessage());
            self::assertSame([
                'type' => 'https://datatracker.ietf.org/doc/html/rfc2616#section-10.4.7',
                'status' => 406,
                'title' => 'Not Acceptable',
                'detail' => 'Not supported acceptLanguage, supportedValues: "en-US"',
                'instance' => null,
                'value' => 'en-US, en;q=0.9',
                'supportedValues' => [
                    0 => 'en-US',
                ],
            ], $e->jsonSerialize());
        }
    }

    public function testProcessWithMatching(): void
    {
        /** @var MockObject|ServerRequestInterface $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('withAttribute')->with('acceptLanguage', 'en-US')->willReturnSelf(),
        ]);

        /** @var MockObject|ResponseInterface $response */
        $response = $this->getMockByCalls(ResponseInterface::class, []);

        /** @var MockObject|RequestHandlerInterface $handler */
        $handler = $this->getMockByCalls(RequestHandlerInterface::class, [
            Call::create('handle')->with($request)->willReturn($response),
        ]);

        /** @var MockObject|NegotiatedValueInterface $negotiatedValue */
        $negotiatedValue = $this->getMockByCalls(NegotiatedValueInterface::class, [
            Call::create('getValue')->with()->willReturn('en-US'),
        ]);

        /** @var AcceptLanguageNegotiatorInterface|MockObject $acceptLanguageNegotiator */
        $acceptLanguageNegotiator = $this->getMockByCalls(AcceptLanguageNegotiatorInterface::class, [
            Call::create('negotiate')->with($request)->willReturn($negotiatedValue),
        ]);

        $middleware = new AcceptLanguageMiddleware($acceptLanguageNegotiator);

        $response = $middleware->process($request, $handler);
    }
}
