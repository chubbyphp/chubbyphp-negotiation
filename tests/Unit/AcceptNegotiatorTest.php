<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Negotiation\Unit;

use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockObjectBuilder;
use Chubbyphp\Negotiation\AcceptNegotiator;
use Chubbyphp\Negotiation\NegotiatedValue;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

/**
 * @covers \Chubbyphp\Negotiation\AcceptNegotiator
 *
 * @internal
 */
final class AcceptNegotiatorTest extends TestCase
{
    public function testGetSupportedMediaTypes(): void
    {
        $negotiator = new AcceptNegotiator(['application/json']);

        self::assertEquals(['application/json'], $negotiator->getSupportedMediaTypes());
    }

    public function testWithoutSupportedMimeTypes(): void
    {
        $builder = new MockObjectBuilder();

        $negotiator = new AcceptNegotiator([]);

        /** @var ServerRequestInterface $request */
        $request = $builder->create(ServerRequestInterface::class, []);

        self::assertNull($negotiator->negotiate($request));
    }

    public function testWithoutHeader(): void
    {
        $builder = new MockObjectBuilder();

        $negotiator = new AcceptNegotiator(['application/json']);

        /** @var ServerRequestInterface $request */
        $request = $builder->create(ServerRequestInterface::class, [
            new WithReturn('hasHeader', ['Accept'], false),
        ]);

        self::assertNull($negotiator->negotiate($request));
    }

    #[DataProvider('provideNegotiateCases')]
    public function testNegotiate(
        ServerRequestInterface $request,
        array $supportedMediaTypes,
        ?NegotiatedValue $expectedAccept = null
    ): void {
        $negotiator = new AcceptNegotiator($supportedMediaTypes);

        self::assertEquals($expectedAccept, $negotiator->negotiate($request));
    }

    public static function provideNegotiateCases(): iterable
    {
        return [
            [
                'request' => self::getRequest('text/html,*/*;q =0.8 ,   application/xhtml+xml; q=1.0,application/xml; q=0.9'),
                'supportedMediaTypes' => ['application/json', 'application/xml', 'application/x-yaml'],
                'expectedAccept' => new NegotiatedValue('application/xml', ['q' => '0.9']),
            ],
            [
                'request' => self::getRequest(
                    'text/html,   application/xhtml+xml,application/xml; q   =   0.9 ,     */    *;q = 0.8'
                ),
                'supportedMediaTypes' => ['application/json', 'application/xml', 'application/x-yaml'],
                'expectedAccept' => new NegotiatedValue('application/xml', ['q' => '0.9']),
            ],
            [
                'request' => self::getRequest('text/html,application/xhtml+xml ,application/xml; q=0.9 ,*/*;  q= 0.8'),
                'supportedMediaTypes' => ['application/json'],
                'expectedAccept' => new NegotiatedValue('application/json', ['q' => '0.8']),
            ],
            [
                'request' => self::getRequest('*/json, */xml'), // cause */value is not a valid mime
                'supportedMediaTypes' => ['application/xml'],
                'expectedAccept' => null,
            ],
            [
                'request' => self::getRequest('application/*;q=0.5, application/json'),
                'supportedMediaTypes' => ['application/xml', 'application/json'],
                'expectedAccept' => new NegotiatedValue('application/json', ['q' => '1.0']),
            ],
            [
                'request' => self::getRequest('application/*, application/json;q=0.5'),
                'supportedMediaTypes' => ['application/xml', 'application/json'],
                'expectedAccept' => new NegotiatedValue('application/json', ['q' => '0.5']),
            ],
            [
                'request' => self::getRequest('application/*, application/json;q=0.5, application/xml;q=0.8'),
                'supportedMediaTypes' => ['text/html'],
                'expectedAccept' => null,
            ],
            [
                'request' => self::getRequest('application/json/json'),
                'supportedMediaTypes' => ['application/json'],
                'expectedAccept' => null,
            ],
            [
                'request' => self::getRequest('application, text, application/*'),
                'supportedMediaTypes' => ['application/json'],
                'expectedAccept' => new NegotiatedValue('application/json', ['q' => '1.0']),
            ],
            [
                'request' => self::getRequest('xml, application/json;q=0.5'),
                'supportedMediaTypes' => ['application/json'],
                'expectedAccept' => new NegotiatedValue('application/json', ['q' => '0.5']),
            ],
            [
                'request' => self::getRequest('xml, application/json; q=0.2, application/*;q=0.5'),
                'supportedMediaTypes' => ['application/json'],
                'expectedAccept' => new NegotiatedValue('application/json', ['q' => '0.2']),
            ],
            [
                'request' => self::getRequest('*/*,application/*;q=0.5'),
                'supportedMediaTypes' => ['text/html', 'application/json'],
                'expectedAccept' => new NegotiatedValue('application/json', ['q' => '0.5']),
            ],
            [
                'request' => self::getRequest('text/html;q=0.1,application/*;q=0.5,application/xml;q=0.9'),
                'supportedMediaTypes' => ['text/html', 'application/json', 'application/xml'],
                'expectedAccept' => new NegotiatedValue('application/xml', ['q' => '0.9']),
            ],
            [
                'request' => self::getRequest('xml, application/xml ; q=0.6, application/json;q=0.5'),
                'supportedMediaTypes' => ['application/json'],
                'expectedAccept' => new NegotiatedValue('application/json', ['q' => '0.5']),
            ],
            [
                'request' => self::getRequest('*/*, application/json;q=0.9, application/xml;q=0.1'),
                'supportedMediaTypes' => ['application/xml'],
                'expectedAccept' => new NegotiatedValue('application/xml', ['q' => '0.1']),
            ],
            [
                'request' => self::getRequest('text/html, application/*;q=0.1'),
                'supportedMediaTypes' => ['application/json'],
                'expectedAccept' => new NegotiatedValue('application/json', ['q' => '0.1']),
            ],
            [
                'request' => self::getRequest('text/html, applicatio[]n./*;q=0.1'),
                'supportedMediaTypes' => ['application/json'],
                'expectedAccept' => null,
            ],
            [
                'request' => self::getRequest('application/json ; q=1.0, application/ld+xml; q=0.8, application/ld+json; q=0.3'),
                'supportedMediaTypes' => ['application/ld+json'],
                'expectedAccept' => new NegotiatedValue('application/ld+json', ['q' => '0.3']),
            ],
            [
                'request' => self::getRequest('application/json ; q=1.0, application/ld+xml; q=0.8, application/ld+json; q=0.3'),
                'supportedMediaTypes' => ['application/ld+yaml', 'application/ld+json', 'application/ld+xml'],
                'expectedAccept' => new NegotiatedValue('application/ld+xml', ['q' => '0.8']),
            ],
            [
                'request' => self::getRequest('application/json ; q=1.0, application/ld+xml; q=0.8'),
                'supportedMediaTypes' => ['application/ld+json'],
                'expectedAccept' => new NegotiatedValue('application/ld+json', ['q' => '1.0']),
            ],
            [
                'request' => self::getRequest('application/json'),
                'supportedMediaTypes' => ['application/vnd.api+json/extra'],
                'expectedAccept' => null,
            ],
            [ // invalid header - semicolon without qvalue key pair
                'request' => self::getRequest('application/json;'),
                'supportedMediaTypes' => ['application/json'],
                'expectedAccept' => new NegotiatedValue('application/json', ['q' => '1.0']),
            ],
            [ // invalid header - semicolon with qvalue key only
                'request' => self::getRequest('application/json;q'),
                'supportedMediaTypes' => ['application/json'],
                'expectedAccept' => new NegotiatedValue('application/json', ['q' => '1.0']),
            ],
            [
                'request' => self::getRequest('application/json;q=0.9'),
                'supportedMediaTypes' => ['application/vnd.api+json'],
                'expectedAccept' => new NegotiatedValue('application/vnd.api+json', ['q' => '0.9']),
            ],
            [
                'request' => self::getRequest('application/*/extra'),
                'supportedMediaTypes' => ['application/json'],
                'expectedAccept' => null,
            ],
        ];
    }

    private static function getRequest(?string $acceptHeader = null): ServerRequestInterface
    {
        return new class($acceptHeader) implements ServerRequestInterface {
            public function __construct(private ?string $acceptHeader) {}

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
                return null !== $this->acceptHeader;
            }

            public function getHeader(string $name): array
            {
                throw new \BadMethodCallException('Not implemented');
            }

            public function getHeaderLine(string $name): string
            {
                return $this->acceptHeader;
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
