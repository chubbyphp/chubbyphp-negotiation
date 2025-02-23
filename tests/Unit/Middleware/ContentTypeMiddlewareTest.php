<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Negotiation\Unit\Middleware;

use Chubbyphp\HttpException\HttpException;
use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockMethod\WithReturnSelf;
use Chubbyphp\Mock\MockObjectBuilder;
use Chubbyphp\Negotiation\ContentTypeNegotiatorInterface;
use Chubbyphp\Negotiation\Middleware\ContentTypeMiddleware;
use Chubbyphp\Negotiation\NegotiatedValueInterface;
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
    public function testProcessWithoutMatching(): void
    {
        $builder = new MockObjectBuilder();

        /** @var ServerRequestInterface $request */
        $request = $builder->create(ServerRequestInterface::class, [
            new WithReturn('getHeaderLine', ['Content-Type'], 'application/xml'),
        ]);

        /** @var RequestHandlerInterface $handler */
        $handler = $builder->create(RequestHandlerInterface::class, []);

        /** @var ContentTypeNegotiatorInterface $contentTypeNegotiator */
        $contentTypeNegotiator = $builder->create(ContentTypeNegotiatorInterface::class, [
            new WithReturn('negotiate', [$request], null),
            new WithReturn('getSupportedMediaTypes', [], ['application/json']),
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
                    'application/json',
                ],
            ], $e->jsonSerialize());
        }
    }

    public function testProcessWithMatching(): void
    {
        $builder = new MockObjectBuilder();

        /** @var ServerRequestInterface $request */
        $request = $builder->create(ServerRequestInterface::class, [
            new WithReturnSelf('withAttribute', ['contentType', 'application/json']),
        ]);

        /** @var ResponseInterface $response */
        $response = $builder->create(ResponseInterface::class, []);

        /** @var RequestHandlerInterface $handler */
        $handler = $builder->create(RequestHandlerInterface::class, [
            new WithReturn('handle', [$request], $response),
        ]);

        /** @var NegotiatedValueInterface $negotiatedValue */
        $negotiatedValue = $builder->create(NegotiatedValueInterface::class, [
            new WithReturn('getValue', [], 'application/json'),
        ]);

        /** @var ContentTypeNegotiatorInterface $contentTypeNegotiator */
        $contentTypeNegotiator = $builder->create(ContentTypeNegotiatorInterface::class, [
            new WithReturn('negotiate', [$request], $negotiatedValue),
        ]);

        $middleware = new ContentTypeMiddleware($contentTypeNegotiator);

        self::assertSame($response, $middleware->process($request, $handler));
    }
}
