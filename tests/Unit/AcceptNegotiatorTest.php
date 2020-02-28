<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Negotiation\Unit;

use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use Chubbyphp\Negotiation\AcceptNegotiator;
use Chubbyphp\Negotiation\NegotiatedValue;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @covers \Chubbyphp\Negotiation\AcceptNegotiator
 *
 * @internal
 */
final class AcceptNegotiatorTest extends TestCase
{
    use MockByCallsTrait;

    public function testGetSupportedMediaTypes(): void
    {
        $negotiator = new AcceptNegotiator(['application/json']);

        self::assertEquals(['application/json'], $negotiator->getSupportedMediaTypes());
    }

    public function testWithoutSupportedMimeTypes(): void
    {
        $negotiator = new AcceptNegotiator([]);

        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class);

        self::assertNull($negotiator->negotiate($request));
    }

    public function testWithoutHeader(): void
    {
        $negotiator = new AcceptNegotiator(['application/json']);

        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('hasHeader')->with('Accept')->willReturn(false),
        ]);

        self::assertNull($negotiator->negotiate($request));
    }

    /**
     * @dataProvider getToNegotiateHeaders
     */
    public function testNegotiate(
        ServerRequestInterface $request,
        array $supportedMediaTypes,
        NegotiatedValue $expectedAccept = null
    ): void {
        $negotiator = new AcceptNegotiator($supportedMediaTypes);

        self::assertEquals($expectedAccept, $negotiator->negotiate($request));
    }

    public function getToNegotiateHeaders(): array
    {
        return [
            [
                'request' => $this->getRequest('text/html,*/*;q =0.8 ,   application/xhtml+xml; q=1.0,application/xml; q=0.9'),
                'supportedMediaTypes' => ['application/json', 'application/xml', 'application/x-yaml'],
                'expectedAccept' => new NegotiatedValue('application/xml', ['q' => '0.9']),
            ],
            [
                'request' => $this->getRequest(
                    'text/html,   application/xhtml+xml,application/xml; q   =   0.9 ,     */    *;q = 0.8'
                ),
                'supportedMediaTypes' => ['application/json', 'application/xml', 'application/x-yaml'],
                'expectedAccept' => new NegotiatedValue('application/xml', ['q' => '0.9']),
            ],
            [
                'request' => $this->getRequest('text/html,application/xhtml+xml ,application/xml; q=0.9 ,*/*;  q= 0.8'),
                'supportedMediaTypes' => ['application/json'],
                'expectedAccept' => new NegotiatedValue('application/json', ['q' => '0.8']),
            ],
            [
                'request' => $this->getRequest('*/json, */xml'), // cause */value is not a valid mime
                'supportedMediaTypes' => ['application/xml'],
                'expectedAccept' => null,
            ],
            [
                'request' => $this->getRequest('application/*;q=0.5, application/json'),
                'supportedMediaTypes' => ['application/xml', 'application/json'],
                'expectedAccept' => new NegotiatedValue('application/json', ['q' => '1.0']),
            ],
            [
                'request' => $this->getRequest('application/*, application/json;q=0.5'),
                'supportedMediaTypes' => ['application/xml', 'application/json'],
                'expectedAccept' => new NegotiatedValue('application/json', ['q' => '0.5']),
            ],
            [
                'request' => $this->getRequest('application/*, application/json;q=0.5, application/xml;q=0.8'),
                'supportedMediaTypes' => ['text/html'],
                'expectedAccept' => null,
            ],
            [
                'request' => $this->getRequest('application/json/json'),
                'supportedMediaTypes' => ['application/json'],
                'expectedAccept' => null,
            ],
            [
                'request' => $this->getRequest('application, text, application/*'),
                'supportedMediaTypes' => ['application/json'],
                'expectedAccept' => new NegotiatedValue('application/json', ['q' => '1.0']),
            ],
            [
                'request' => $this->getRequest('xml, application/json;q=0.5'),
                'supportedMediaTypes' => ['application/json'],
                'expectedAccept' => new NegotiatedValue('application/json', ['q' => '0.5']),
            ],
            [
                'request' => $this->getRequest('xml, application/json; q=0.2, application/*;q=0.5'),
                'supportedMediaTypes' => ['application/json'],
                'expectedAccept' => new NegotiatedValue('application/json', ['q' => '0.2']),
            ],
            [
                'request' => $this->getRequest('*/*,application/*;q=0.5'),
                'supportedMediaTypes' => ['text/html', 'application/json'],
                'expectedAccept' => new NegotiatedValue('application/json', ['q' => '0.5']),
            ],
            [
                'request' => $this->getRequest('text/html;q=0.1,application/*;q=0.5,application/xml;q=0.9'),
                'supportedMediaTypes' => ['text/html', 'application/json', 'application/xml'],
                'expectedAccept' => new NegotiatedValue('application/xml', ['q' => '0.9']),
            ],
            [
                'request' => $this->getRequest('xml, application/xml ; q=0.6, application/json;q=0.5'),
                'supportedMediaTypes' => ['application/json'],
                'expectedAccept' => new NegotiatedValue('application/json', ['q' => '0.5']),
            ],
            [
                'request' => $this->getRequest('*/*, application/json;q=0.9, application/xml;q=0.1'),
                'supportedMediaTypes' => ['application/xml'],
                'expectedAccept' => new NegotiatedValue('application/xml', ['q' => '0.1']),
            ],
            [
                'request' => $this->getRequest('text/html, application/*;q=0.1'),
                'supportedMediaTypes' => ['application/json'],
                'expectedAccept' => new NegotiatedValue('application/json', ['q' => '0.1']),
            ],
            [
                'request' => $this->getRequest('text/html, applicatio[]n./*;q=0.1'),
                'supportedMediaTypes' => ['application/json'],
                'expectedAccept' => null,
            ],
            [
                'request' => $this->getRequest('application/json ; q=1.0, application/ld+xml; q=0.8, application/ld+json; q=0.3'),
                'supportedMediaTypes' => ['application/ld+json'],
                'expectedAccept' => new NegotiatedValue('application/ld+json', ['q' => '0.3']),
            ],
            [
                'request' => $this->getRequest('application/json ; q=1.0, application/ld+xml; q=0.8, application/ld+json; q=0.3'),
                'supportedMediaTypes' => ['application/ld+yaml', 'application/ld+json', 'application/ld+xml'],
                'expectedAccept' => new NegotiatedValue('application/ld+xml', ['q' => '0.8']),
            ],
            [
                'request' => $this->getRequest('application/json ; q=1.0, application/ld+xml; q=0.8'),
                'supportedMediaTypes' => ['application/ld+json'],
                'expectedAccept' => new NegotiatedValue('application/ld+json', ['q' => '1.0']),
            ],
            [ // invalid header - semicolon without qvalue key pair
                'request' => $this->getRequest('application/json;'),
                'supportedMediaTypes' => ['application/json'],
                'expectedAccept' => new NegotiatedValue('application/json', ['q' => '1.0']),
            ],
            [ // invalid header - semicolon with qvalue key only
                'request' => $this->getRequest('application/json;q'),
                'supportedMediaTypes' => ['application/json'],
                'expectedAccept' => new NegotiatedValue('application/json', ['q' => '1.0']),
            ],
        ];
    }

    private function getRequest(string $acceptHeader = null): ServerRequestInterface
    {
        if (null === $acceptHeader) {
            return $this->getMockByCalls(ServerRequestInterface::class, [
                Call::create('hasHeader')->with('Accept-Language')->willReturn(false),
            ]);
        }

        return $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('hasHeader')->with('Accept')->willReturn(true),
            Call::create('getHeaderLine')->with('Accept')->willReturn($acceptHeader),
        ]);
    }
}
