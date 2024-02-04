<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Negotiation\Unit\Middleware;

use Chubbyphp\HttpException\HttpException;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use Chubbyphp\Negotiation\ContentTypeNegotiatorInterface;
use Chubbyphp\Negotiation\Middleware\ContentTypeMiddleware;
use Chubbyphp\Negotiation\NegotiatedValueInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * @covers \Chubbyphp\Negotiation\Middleware\ContentTypeMiddleware
 *
 * @internal
 */
final class ContentTypeMiddlewareTest extends TestCase
{
    use MockByCallsTrait;

    public function testProcessWithoutMatching(): void
    {
        /** @var MockObject|ServerRequestInterface $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getHeaderLine')->with('Content-Type')->willReturn('application/xml'),
        ]);

        /** @var MockObject|RequestHandlerInterface $handler */
        $handler = $this->getMockByCalls(RequestHandlerInterface::class, []);

        /** @var ContentTypeNegotiatorInterface|MockObject $contentTypeNegotiator */
        $contentTypeNegotiator = $this->getMockByCalls(ContentTypeNegotiatorInterface::class, [
            Call::create('negotiate')->with($request)->willReturn(null),
            Call::create('getSupportedMediaTypes')->with()->willReturn(['application/json']),
        ]);

        $middleware = new ContentTypeMiddleware($contentTypeNegotiator);

        try {
            $middleware->process($request, $handler);

            throw new \Exception('code should not be reached');
        } catch (HttpException $e) {
            self::assertSame('Unsupported Media Type', $e->getMessage());
            self::assertSame([
                'type' => 'https://datatracker.ietf.org/doc/html/rfc2616#section-10.4.16',
                'status' => 415,
                'title' => 'Unsupported Media Type',
                'detail' => 'Not supported content-type, supportedValues: "application/json"',
                'instance' => null,
                'value' => 'application/xml',
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
            Call::create('withAttribute')->with('contentType', 'application/json')->willReturnSelf(),
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

        /** @var ContentTypeNegotiatorInterface|MockObject $contentTypeNegotiator */
        $contentTypeNegotiator = $this->getMockByCalls(ContentTypeNegotiatorInterface::class, [
            Call::create('negotiate')->with($request)->willReturn($negotiatedValue),
        ]);

        $middleware = new ContentTypeMiddleware($contentTypeNegotiator);

        $response = $middleware->process($request, $handler);
    }
}
