<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Negotiation\Unit;

use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use Chubbyphp\Negotiation\AcceptLanguageNegotiator;
use Chubbyphp\Negotiation\NegotiatedValue;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

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
        $request = $this->getMockByCalls(ServerRequestInterface::class);

        self::assertNull($negotiator->negotiate($request));
    }

    public function testWithoutHeader(): void
    {
        $negotiator = new AcceptLanguageNegotiator(['en']);

        /** @var MockObject|ServerRequestInterface $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('hasHeader')->with('Accept-Language')->willReturn(false),
        ]);

        self::assertNull($negotiator->negotiate($request));
    }

    /**
     * @dataProvider getToNegotiateHeaders
     */
    public function testNegotiate(
        ServerRequestInterface $request,
        array $supportedLocales,
        ?NegotiatedValue $expectedAcceptLanguage = null
    ): void {
        $negotiator = new AcceptLanguageNegotiator($supportedLocales);

        self::assertEquals($expectedAcceptLanguage, $negotiator->negotiate($request));
    }

    public function getToNegotiateHeaders(): array
    {
        return [
            [
                'request' => $this->getRequest('de,en;q=0.3,en-US;q=0.7'),
                'supportedMediaTypes' => ['en', 'de'],
                'expectedAcceptLanguage' => new NegotiatedValue('de', ['q' => '1.0']),
            ],
            [
                'request' => $this->getRequest('de, en -US;q    =0.7,en;     q=0.3'),
                'supportedMediaTypes' => ['en', 'de'],
                'expectedAcceptLanguage' => new NegotiatedValue('de', ['q' => '1.0']),
            ],
            [
                'request' => $this->getRequest('de,en;q=0.3,en   - US ; q = 0.7'),
                'supportedMediaTypes' => ['en'],
                'expectedAcceptLanguage' => new NegotiatedValue('en', ['q' => '0.3']),
            ],
            [
                'request' => $this->getRequest('de,                       en ; q                   =         0.3   '),
                'supportedMediaTypes' => ['en'],
                'expectedAcceptLanguage' => new NegotiatedValue('en', ['q' => '0.3']),
            ],
            [
                'request' => $this->getRequest('pt ; q= 0.5,de,en;q=0.3'),
                'supportedMediaTypes' => ['fr'],
                'expectedAcceptLanguage' => null,
            ],
            [
                'request' => $this->getRequest('en-US;q=0.7,*;q=0.3,fr; q=0.8'),
                'supportedMediaTypes' => ['de'],
                'expectedAcceptLanguage' => new NegotiatedValue('de', ['q' => '0.3']),
            ],
            [
                'request' => $this->getRequest('en-US;q=0.7,*;q=0.3,fr; q=0.8'),
                'supportedMediaTypes' => ['fr'],
                'expectedAcceptLanguage' => new NegotiatedValue('fr', ['q' => '0.8']),
            ],
            [
                'request' => $this->getRequest('en; q=0.1, fr; q=0.4, fu; q=0.9, de; q=0.2'),
                'supportedMediaTypes' => ['de', 'fu', 'en'],
                'expectedAcceptLanguage' => new NegotiatedValue('fu', ['q' => '0.9']),
            ],
            [
                'request' => $this->getRequest('de-CH,de;q=0.8'),
                'supportedMediaTypes' => ['de'],
                'expectedAcceptLanguage' => new NegotiatedValue('de', ['q' => '0.8']),
            ],
            [
                'request' => $this->getRequest('de-CH'),
                'supportedMediaTypes' => ['de'],
                'expectedAcceptLanguage' => new NegotiatedValue('de', ['q' => '1.0']),
            ],
            [
                'request' => $this->getRequest('de'),
                'supportedMediaTypes' => ['de-CH'],
                'expectedAcceptLanguage' => null,
            ],
            [
                'request' => $this->getRequest('*,de;q=0.1'),
                'supportedMediaTypes' => ['de'],
                'expectedAcceptLanguage' => new NegotiatedValue('de', ['q' => '0.1']),
            ],
            [
                'request' => $this->getRequest('de-DE-AT,en-US'),
                'supportedMediaTypes' => ['de'],
                'expectedAcceptLanguage' => null,
            ],
            [
                'request' => $this->getRequest('en,fr,it,de-CH'),
                'supportedMediaTypes' => ['de'],
                'expectedAcceptLanguage' => new NegotiatedValue('de', ['q' => '1.0']),
            ],
            [ // invalid header - semicolon without qvalue key pair
                'request' => $this->getRequest('de;'),
                'supportedMediaTypes' => ['de'],
                'expectedAcceptLanguage' => new NegotiatedValue('de', ['q' => '1.0']),
            ],
            [ // invalid header - semicolon with qvalue key only
                'request' => $this->getRequest('de;q'),
                'supportedMediaTypes' => ['de'],
                'expectedAcceptLanguage' => new NegotiatedValue('de', ['q' => '1.0']),
            ],
        ];
    }

    private function getRequest(?string $acceptHeader = null): ServerRequestInterface
    {
        if (null === $acceptHeader) {
            return $this->getMockByCalls(ServerRequestInterface::class, [
                Call::create('hasHeader')->with('Accept-Language')->willReturn(false),
            ]);
        }

        return $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('hasHeader')->with('Accept-Language')->willReturn(true),
            Call::create('getHeaderLine')->with('Accept-Language')->willReturn($acceptHeader),
        ]);
    }
}
