<?php

namespace Chubbyphp\Tests\Negotiation;

use Chubbyphp\Negotiation\AcceptLanguageNegotiator;
use Chubbyphp\Negotiation\NegotiatedValue;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * @covers \Chubbyphp\Negotiation\AcceptLanguageNegotiator
 */
final class AcceptLanguageNegotiatorTest extends TestCase
{
    public function testGetSupportedLocales()
    {
        $negotiator = new AcceptLanguageNegotiator(['en']);

        self::assertEquals(['en'], $negotiator->getSupportedLocales());
    }

    public function testWithoutSupportedMimeTypes()
    {
        $negotiator = new AcceptLanguageNegotiator([]);

        self::assertNull($negotiator->negotiate($this->getRequest()));
    }

    public function testWithoutHeader()
    {
        $negotiator = new AcceptLanguageNegotiator(['en']);

        self::assertNull($negotiator->negotiate($this->getRequest()));
    }

    /**
     * @dataProvider getToNegotiateHeaders
     *
     * @param Request              $request
     * @param array                $supportedLocales
     * @param NegotiatedValue|null $expectedAcceptLanguage
     */
    public function testNegotiate(Request $request, array $supportedLocales, NegotiatedValue $expectedAcceptLanguage = null)
    {
        $negotiator = new AcceptLanguageNegotiator($supportedLocales);

        self::assertEquals($expectedAcceptLanguage, $negotiator->negotiate($request));
    }

    public function getToNegotiateHeaders(): array
    {
        return [
            [
                'request' => $this->getRequest('de,en-US;q=0.7,en;q=0.3'),
                'supportedMediaTypes' => ['en', 'de'],
                'expectedAcceptLanguage' => new NegotiatedValue('de', ['q' => '1.0']),
            ],
            [
                'request' => $this->getRequest('de,en;q=0.3'),
                'supportedMediaTypes' => ['en'],
                'expectedAcceptLanguage' => new NegotiatedValue('en', ['q' => '0.3']),
            ],
            [
                'request' => $this->getRequest('de,en;q=0.3'),
                'supportedMediaTypes' => ['fr'],
                'expectedAcceptLanguage' => null,
            ],
            [
                'request' => $this->getRequest('en-US;q=0.7,*;q=0.3'),
                'supportedMediaTypes' => ['de'],
                'expectedAcceptLanguage' => new NegotiatedValue('de', ['q' => '0.3']),
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
                'request' => $this->getRequest('de-DE-AT'),
                'supportedMediaTypes' => ['de'],
                'expectedAcceptLanguage' => null,
            ],
        ];
    }

    /**
     * @param string|null $acceptHeader
     *
     * @return Request
     */
    private function getRequest(string $acceptHeader = null): Request
    {
        /** @var Request|\PHPUnit_Framework_MockObject_MockObject $request */
        $request = $this->getMockBuilder(Request::class)
            ->setMethods(['hasHeader', 'getHeaderLine'])
            ->getMockForAbstractClass();

        $request->expects(self::any())->method('hasHeader')->with('Accept-Language')->willReturn(null !== $acceptHeader);
        $request->expects(self::any())->method('getHeaderLine')->with('Accept-Language')->willReturn($acceptHeader);

        return $request;
    }
}
