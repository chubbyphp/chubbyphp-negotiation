<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Negotiation\Unit\Middleware;

use Chubbyphp\HttpException\HttpException;
use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockMethod\WithReturnSelf;
use Chubbyphp\Mock\MockObjectBuilder;
use Chubbyphp\Negotiation\AcceptNegotiatorInterface;
use Chubbyphp\Negotiation\Middleware\AcceptMiddleware;
use Chubbyphp\Negotiation\NegotiatedValueInterface;
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
    public function testProcessWithoutMatching(): void
    {
        $builder = new MockObjectBuilder();

        /** @var ServerRequestInterface $request */
        $request = $builder->create(ServerRequestInterface::class, [
            new WithReturn('getHeaderLine', ['Accept'], 'application/xml, application/x-yaml;q=0.9'),
        ]);

        /** @var RequestHandlerInterface $handler */
        $handler = $builder->create(RequestHandlerInterface::class, []);

        /** @var AcceptNegotiatorInterface $acceptNegotiator */
        $acceptNegotiator = $builder->create(AcceptNegotiatorInterface::class, [
            new WithReturn('negotiate', [$request], null),
            new WithReturn('getSupportedMediaTypes', [], ['application/json']),
        ]);

        $middleware = new AcceptMiddleware($acceptNegotiator);

        try {
            $middleware->process($request, $handler);

            throw new \Exception('code should not be reached');
        } catch (HttpException $e) {
            self::assertSame('Not Acceptable', $e->getMessage());
            self::assertSame([
                'type' => 'https://datatracker.ietf.org/doc/html/rfc2616#section-10.4.7',
                'status' => 406,
                'title' => 'Not Acceptable',
                'detail' => 'Not supported accept, supportedValues: "application/json"',
                'instance' => null,
                'value' => 'application/xml, application/x-yaml;q=0.9',
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
            new WithReturnSelf('withAttribute', ['accept', 'application/json']),
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

        /** @var AcceptNegotiatorInterface $acceptNegotiator */
        $acceptNegotiator = $builder->create(AcceptNegotiatorInterface::class, [
            new WithReturn('negotiate', [$request], $negotiatedValue),
        ]);

        $middleware = new AcceptMiddleware($acceptNegotiator);

        self::assertSame($response, $middleware->process($request, $handler));
    }
}
