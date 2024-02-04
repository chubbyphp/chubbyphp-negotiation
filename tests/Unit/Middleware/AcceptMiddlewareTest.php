<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Negotiation\Unit\Middleware;

use Chubbyphp\HttpException\HttpException;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use Chubbyphp\Negotiation\AcceptNegotiatorInterface;
use Chubbyphp\Negotiation\Middleware\AcceptMiddleware;
use Chubbyphp\Negotiation\NegotiatedValueInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * @covers \Chubbyphp\Negotiation\Middleware\AcceptMiddleware
 *
 * @internal
 */
final class AcceptMiddlewareTest extends TestCase
{
    use MockByCallsTrait;

    public function testProcessWithoutMatching(): void
    {
        /** @var MockObject|ServerRequestInterface $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getHeaderLine')->with('Accept')->willReturn('application/xml, application/x-yaml;q=0.9'),
        ]);

        /** @var MockObject|RequestHandlerInterface $handler */
        $handler = $this->getMockByCalls(RequestHandlerInterface::class, []);

        /** @var AcceptNegotiatorInterface|MockObject $acceptNegotiator */
        $acceptNegotiator = $this->getMockByCalls(AcceptNegotiatorInterface::class, [
            Call::create('negotiate')->with($request)->willReturn(null),
            Call::create('getSupportedMediaTypes')->with()->willReturn(['application/json']),
        ]);

        $middleware = new AcceptMiddleware($acceptNegotiator);

        try {
            $middleware->process($request, $handler);

            throw new \Exception('code should not be reached');
        } catch (HttpException $e) {
            self::assertSame('Not Acceptable', $e->getMessage());
            self::assertSame(['type' => 'https://datatracker.ietf.org/doc/html/rfc2616#section-10.4.7',
                'status' => 406,
                'title' => 'Not Acceptable',
                'detail' => 'Not supported accept, supportedValues: "application/json"',
                'instance' => null,
                'value' => 'application/xml, application/x-yaml;q=0.9',
                'supportedValues' => [
                    0 => 'application/json',
                ],
            ], $e->jsonSerialize());
        }
    }

    public function testProcessWithMatching(): void
    {
        /** @var MockObject|ServerRequestInterface $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('withAttribute')->with('accept', 'application/json')->willReturnSelf(),
        ]);

        /** @var MockObject|ResponseInterface $response */
        $response = $this->getMockByCalls(ResponseInterface::class, []);

        /** @var MockObject|RequestHandlerInterface $handler */
        $handler = $this->getMockByCalls(RequestHandlerInterface::class, [
            Call::create('handle')->with($request)->willReturn($response),
        ]);

        /** @var MockObject|NegotiatedValueInterface $negotiatedValue */
        $negotiatedValue = $this->getMockByCalls(NegotiatedValueInterface::class, [
            Call::create('getValue')->with()->willReturn('application/json'),
        ]);

        /** @var AcceptNegotiatorInterface|MockObject $acceptNegotiator */
        $acceptNegotiator = $this->getMockByCalls(AcceptNegotiatorInterface::class, [
            Call::create('negotiate')->with($request)->willReturn($negotiatedValue),
        ]);

        $middleware = new AcceptMiddleware($acceptNegotiator);

        $response = $middleware->process($request, $handler);
    }
}
