<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Negotiation\Unit;

use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use Chubbyphp\Negotiation\AcceptLanguageNegotiator;
use Chubbyphp\Negotiation\NegotiatedValue;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

/**
 * @covers \Chubbyphp\Negotiation\AcceptLanguageNegotiator
 *
 * @internal
 */
final class AcceptLanguageNegotiatorTest extends TestCase
{
    use MockByCallsTrait;

    public function testGetSupportedLocales(): void
    {
        $negotiator = new AcceptLanguageNegotiator(['en']);

        self::assertEquals(['en'], $negotiator->getSupportedLocales());
    }

    public function testWithoutSupportedMimeTypes(): void
    {
        $negotiator = new AcceptLanguageNegotiator([]);

        /** @var MockObject|ServerRequestInterface $request */
        $request = self::getMockByCalls(ServerRequestInterface::class);

        self::assertNull($negotiator->negotiate($request));
    }

    public function testWithoutHeader(): void
    {
        $negotiator = new AcceptLanguageNegotiator(['en']);

        /** @var MockObject|ServerRequestInterface $request */
        $request = self::getMockByCalls(ServerRequestInterface::class, [
            Call::create('hasHeader')->with('Accept-Language')->willReturn(false),
        ]);

        self::assertNull($negotiator->negotiate($request));
    }

    /**
     * @dataProvider provideNegotiateCases
     */
    public function testNegotiate(
        ServerRequestInterface $request,
        array $supportedLocales,
        ?NegotiatedValue $expectedAcceptLanguage = null
    ): void {
        $negotiator = new AcceptLanguageNegotiator($supportedLocales);

        self::assertEquals($expectedAcceptLanguage, $negotiator->negotiate($request));
    }

    public static function provideNegotiateCases(): iterable
    {
        return [
            [
                'request' => self::getRequest('de,en;q=0.3,en-US;q=0.7'),
                'supportedLocales' => ['en', 'de'],
                'expectedAcceptLanguage' => new NegotiatedValue('de', ['q' => '1.0']),
            ],
            [
                'request' => self::getRequest('de, en -US;q    =0.7,en;     q=0.3'),
                'supportedLocales' => ['en', 'de'],
                'expectedAcceptLanguage' => new NegotiatedValue('de', ['q' => '1.0']),
            ],
            [
                'request' => self::getRequest('de,en;q=0.3,en   - US ; q = 0.7'),
                'supportedLocales' => ['en'],
                'expectedAcceptLanguage' => new NegotiatedValue('en', ['q' => '0.3']),
            ],
            [
                'request' => self::getRequest('de,                       en ; q                   =         0.3   '),
                'supportedLocales' => ['en'],
                'expectedAcceptLanguage' => new NegotiatedValue('en', ['q' => '0.3']),
            ],
            [
                'request' => self::getRequest('pt ; q= 0.5,de,en;q=0.3'),
                'supportedLocales' => ['fr'],
                'expectedAcceptLanguage' => null,
            ],
            [
                'request' => self::getRequest('en-US;q=0.7,*;q=0.3,fr; q=0.8'),
                'supportedLocales' => ['de'],
                'expectedAcceptLanguage' => new NegotiatedValue('de', ['q' => '0.3']),
            ],
            [
                'request' => self::getRequest('en-US;q=0.7,*;q=0.3,fr; q=0.8'),
                'supportedLocales' => ['fr'],
                'expectedAcceptLanguage' => new NegotiatedValue('fr', ['q' => '0.8']),
            ],
            [
                'request' => self::getRequest('en; q=0.1, fr; q=0.4, fu; q=0.9, de; q=0.2'),
                'supportedLocales' => ['de', 'fu', 'en'],
                'expectedAcceptLanguage' => new NegotiatedValue('fu', ['q' => '0.9']),
            ],
            [
                'request' => self::getRequest('de-CH,de;q=0.8'),
                'supportedLocales' => ['de'],
                'expectedAcceptLanguage' => new NegotiatedValue('de', ['q' => '0.8']),
            ],
            [
                'request' => self::getRequest('de-CH'),
                'supportedLocales' => ['de'],
                'expectedAcceptLanguage' => new NegotiatedValue('de', ['q' => '1.0']),
            ],
            [
                'request' => self::getRequest('de'),
                'supportedLocales' => ['de-CH'],
                'expectedAcceptLanguage' => null,
            ],
            [
                'request' => self::getRequest('*,de;q=0.1'),
                'supportedLocales' => ['de'],
                'expectedAcceptLanguage' => new NegotiatedValue('de', ['q' => '0.1']),
            ],
            [
                'request' => self::getRequest('de-DE-AT,en-US'),
                'supportedLocales' => ['de'],
                'expectedAcceptLanguage' => null,
            ],
            [
                'request' => self::getRequest('en,fr,it,de-CH'),
                'supportedLocales' => ['de'],
                'expectedAcceptLanguage' => new NegotiatedValue('de', ['q' => '1.0']),
            ],
            [ // invalid header - semicolon without qvalue key pair
                'request' => self::getRequest('de;'),
                'supportedLocales' => ['de'],
                'expectedAcceptLanguage' => new NegotiatedValue('de', ['q' => '1.0']),
            ],
            [ // invalid header - semicolon with qvalue key only
                'request' => self::getRequest('de;q'),
                'supportedLocales' => ['de'],
                'expectedAcceptLanguage' => new NegotiatedValue('de', ['q' => '1.0']),
            ],
        ];
    }

    private static function getRequest(?string $acceptLanguageHeader = null): ServerRequestInterface
    {
        return new class($acceptLanguageHeader) implements ServerRequestInterface {
            public function __construct(private ?string $acceptLanguageHeader) {}

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
                return null !== $this->acceptLanguageHeader;
            }

            public function getHeader(string $name): array
            {
                throw new \BadMethodCallException('Not implemented');
            }

            public function getHeaderLine(string $name): string
            {
                return $this->acceptLanguageHeader;
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
