<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Negotiation\Unit\Middleware;

use Chubbyphp\HttpException\HttpException;
use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockMethod\WithReturnSelf;
use Chubbyphp\Mock\MockObjectBuilder;
use Chubbyphp\Negotiation\AcceptLanguageNegotiatorInterface;
use Chubbyphp\Negotiation\Middleware\AcceptLanguageMiddleware;
use Chubbyphp\Negotiation\NegotiatedValueInterface;
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
    public function testProcessWithoutMatching(): void
    {
        $builder = new MockObjectBuilder();

        /** @var ServerRequestInterface $request */
        $request = $builder->create(ServerRequestInterface::class, [
            new WithReturn('getHeaderLine', ['Accept-Language'], 'en-US, en;q=0.9'),
        ]);

        /** @var RequestHandlerInterface $handler */
        $handler = $builder->create(RequestHandlerInterface::class, []);

        /** @var AcceptLanguageNegotiatorInterface $acceptLanguageNegotiator */
        $acceptLanguageNegotiator = $builder->create(AcceptLanguageNegotiatorInterface::class, [
            new WithReturn('negotiate', [$request], null),
            new WithReturn('getSupportedLocales', [], ['en-US']),
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
                    'en-US',
                ],
            ], $e->jsonSerialize());
        }
    }

    public function testProcessWithMatching(): void
    {
        $builder = new MockObjectBuilder();

        /** @var ServerRequestInterface $request */
        $request = $builder->create(ServerRequestInterface::class, [
            new WithReturnSelf('withAttribute', ['acceptLanguage', 'en-US']),
        ]);

        /** @var ResponseInterface $response */
        $response = $builder->create(ResponseInterface::class, []);

        /** @var RequestHandlerInterface $handler */
        $handler = $builder->create(RequestHandlerInterface::class, [
            new WithReturn('handle', [$request], $response),
        ]);

        /** @var NegotiatedValueInterface $negotiatedValue */
        $negotiatedValue = $builder->create(NegotiatedValueInterface::class, [
            new WithReturn('getValue', [], 'en-US'),
        ]);

        /** @var AcceptLanguageNegotiatorInterface $acceptLanguageNegotiator */
        $acceptLanguageNegotiator = $builder->create(AcceptLanguageNegotiatorInterface::class, [
            new WithReturn('negotiate', [$request], $negotiatedValue),
        ]);

        $middleware = new AcceptLanguageMiddleware($acceptLanguageNegotiator);

        self::assertSame($response, $middleware->process($request, $handler));
    }
}
