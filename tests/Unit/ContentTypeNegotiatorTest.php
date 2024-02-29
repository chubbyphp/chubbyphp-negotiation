<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Negotiation\Unit;

use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use Chubbyphp\Negotiation\ContentTypeNegotiator;
use Chubbyphp\Negotiation\NegotiatedValue;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

/**
 * @covers \Chubbyphp\Negotiation\ContentTypeNegotiator
 *
 * @internal
 */
final class ContentTypeNegotiatorTest extends TestCase
{
    use MockByCallsTrait;

    public function testGetSupportedMediaTypes(): void
    {
        $negotiator = new ContentTypeNegotiator(['application/json']);

        self::assertEquals(['application/json'], $negotiator->getSupportedMediaTypes());
    }

    public function testWithoutSupportedMimeTypes(): void
    {
        $negotiator = new ContentTypeNegotiator([]);

        /** @var MockObject|ServerRequestInterface $request */
        $request = self::getMockByCalls(ServerRequestInterface::class);

        self::assertNull($negotiator->negotiate($request));
    }

    public function testWithoutHeader(): void
    {
        $negotiator = new ContentTypeNegotiator(['application/json']);

        /** @var MockObject|ServerRequestInterface $request */
        $request = self::getMockByCalls(ServerRequestInterface::class, [
            Call::create('hasHeader')->with('Content-Type')->willReturn(false),
        ]);

        self::assertNull($negotiator->negotiate($request));
    }

    /**
     * @dataProvider provideNegotiateCases
     */
    public function testNegotiate(
        ServerRequestInterface $request,
        array $supportedMediaTypes,
        ?NegotiatedValue $expectedContentType = null
    ): void {
        $negotiator = new ContentTypeNegotiator($supportedMediaTypes);

        self::assertEquals($expectedContentType, $negotiator->negotiate($request));
    }

    public static function provideNegotiateCases(): iterable
    {
        return [
            [
                'request' => self::getRequest(' application/xml ; charset = UTF-8 '),
                'supportedMediaTypes' => ['application/json', 'application/xml', 'application/x-yaml'],
                'expectedContentType' => new NegotiatedValue('application/xml', ['charset' => 'UTF-8']),
            ],
            [
                'request' => self::getRequest('application/xml                 ; charset=UTF-8'),
                'supportedMediaTypes' => ['application/json'],
                'expectedContentType' => null,
            ],
            [
                'request' => self::getRequest('application/xml; charset=UTF-8,'), // invalid format
                'supportedMediaTypes' => ['application/json', 'application/xml', 'application/x-yaml'],
                'expectedContentType' => null,
            ],
            [
                'request' => self::getRequest('xml; charset=UTF-8'), // invalid format
                'supportedMediaTypes' => ['application/xml'],
                'expectedContentType' => null,
            ],
            [
                'request' => self::getRequest('application/jsonx+xml; charset=UTF-8'),
                'supportedMediaTypes' => ['application/xml'],
                'expectedContentType' => new NegotiatedValue('application/xml', ['charset' => 'UTF-8']),
            ],
            [
                'request' => self::getRequest('application/jsonx+xml; charset=UTF-8'),
                'supportedMediaTypes' => ['application/json'],
                'expectedContentType' => null,
            ],
        ];
    }

    private static function getRequest(?string $contentTypeHeader = null): ServerRequestInterface
    {
        return new class($contentTypeHeader) implements ServerRequestInterface {
            public function __construct(private ?string $contentTypeHeader) {}

            public function getServerParams(): array
            {
                throw new \BadMethodCallException('Not implemented');
            }

            public function getCookieParams(): array
            {
                throw new \BadMethodCallException('Not implemented');
            }

            public function withCookieParams(array $cookies): ServerRequestInterface
            {
                throw new \BadMethodCallException('Not implemented');
            }

            public function getQueryParams(): array
            {
                throw new \BadMethodCallException('Not implemented');
            }

            public function withQueryParams(array $query): ServerRequestInterface
            {
                throw new \BadMethodCallException('Not implemented');
            }

            public function getUploadedFiles(): array
            {
                throw new \BadMethodCallException('Not implemented');
            }

            public function withUploadedFiles(array $uploadedFiles): ServerRequestInterface
            {
                throw new \BadMethodCallException('Not implemented');
            }

            public function getParsedBody(): void
            {
                throw new \BadMethodCallException('Not implemented');
            }

            public function withParsedBody($data): ServerRequestInterface
            {
                throw new \BadMethodCallException('Not implemented');
            }

            public function getAttributes(): array
            {
                throw new \BadMethodCallException('Not implemented');
            }

            public function getAttribute(string $name, $default = null): void
            {
                throw new \BadMethodCallException('Not implemented');
            }

            public function withAttribute(string $name, $value): ServerRequestInterface
            {
                throw new \BadMethodCallException('Not implemented');
            }

            public function withoutAttribute(string $name): ServerRequestInterface
            {
                throw new \BadMethodCallException('Not implemented');
            }

            public function getRequestTarget(): string
            {
                throw new \BadMethodCallException('Not implemented');
            }

            public function withRequestTarget(string $requestTarget): RequestInterface
            {
                throw new \BadMethodCallException('Not implemented');
            }

            public function getMethod(): string
            {
                throw new \BadMethodCallException('Not implemented');
            }

            public function withMethod(string $method): RequestInterface
            {
                throw new \BadMethodCallException('Not implemented');
            }

            public function getUri(): UriInterface
            {
                throw new \BadMethodCallException('Not implemented');
            }

            public function withUri(UriInterface $uri, bool $preserveHost = false): RequestInterface
            {
                throw new \BadMethodCallException('Not implemented');
            }

            public function getProtocolVersion(): string
            {
                throw new \BadMethodCallException('Not implemented');
            }

            public function withProtocolVersion(string $version): MessageInterface
            {
                throw new \BadMethodCallException('Not implemented');
            }

            public function getHeaders(): array
            {
                throw new \BadMethodCallException('Not implemented');
            }

            public function hasHeader(string $name): bool
            {
                return null !== $this->contentTypeHeader;
            }

            public function getHeader(string $name): array
            {
                throw new \BadMethodCallException('Not implemented');
            }

            public function getHeaderLine(string $name): string
            {
                return $this->contentTypeHeader;
            }

            public function withHeader(string $name, $value): MessageInterface
            {
                throw new \BadMethodCallException('Not implemented');
            }

            public function withAddedHeader(string $name, $value): MessageInterface
            {
                throw new \BadMethodCallException('Not implemented');
            }

            public function withoutHeader(string $name): MessageInterface
            {
                throw new \BadMethodCallException('Not implemented');
            }

            public function getBody(): StreamInterface
            {
                throw new \BadMethodCallException('Not implemented');
            }

            public function withBody(StreamInterface $body): MessageInterface
            {
                throw new \BadMethodCallException('Not implemented');
            }
        };
    }
}
