<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Negotiation;

use Chubbyphp\Negotiation\ContentTypeNegotiator;
use Chubbyphp\Negotiation\NegotiatedValue;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * @covers \Chubbyphp\Negotiation\ContentTypeNegotiator
 *
 * @internal
 */
final class ContentTypeNegotiatorTest extends TestCase
{
    public function testGetSupportedMediaTypes()
    {
        $negotiator = new ContentTypeNegotiator(['application/json']);

        self::assertEquals(['application/json'], $negotiator->getSupportedMediaTypes());
    }

    public function testWithoutSupportedMimeTypes()
    {
        $negotiator = new ContentTypeNegotiator([]);

        self::assertNull($negotiator->negotiate($this->getRequest()));
    }

    public function testWithoutHeader()
    {
        $negotiator = new ContentTypeNegotiator(['application/json']);

        self::assertNull($negotiator->negotiate($this->getRequest()));
    }

    /**
     * @dataProvider getToNegotiateHeaders
     *
     * @param Request              $request
     * @param array                $supportedMediaTypes
     * @param NegotiatedValue|null $expectedContentType
     */
    public function testNegotiate(Request $request, array $supportedMediaTypes, NegotiatedValue $expectedContentType = null)
    {
        $negotiator = new ContentTypeNegotiator($supportedMediaTypes);

        self::assertEquals($expectedContentType, $negotiator->negotiate($request));
    }

    public function getToNegotiateHeaders(): array
    {
        return [
            [
                'request' => $this->getRequest('application/xml; charset=UTF-8'),
                'supportedMediaTypes' => ['application/json', 'application/xml', 'application/x-yaml'],
                'expectedContentType' => new NegotiatedValue('application/xml', ['charset' => 'UTF-8']),
            ],
            [
                'request' => $this->getRequest('application/xml; charset=UTF-8'),
                'supportedMediaTypes' => ['application/json'],
                'expectedContentType' => null,
            ],
            [
                'request' => $this->getRequest('application/xml; charset=UTF-8,'), // invalid format
                'supportedMediaTypes' => ['application/json', 'application/xml', 'application/x-yaml'],
                'expectedContentType' => null,
            ],
            [
                'request' => $this->getRequest('xml; charset=UTF-8'), // invalid format
                'supportedMediaTypes' => ['application/xml'],
                'expectedContentType' => null,
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
            ->getMockForAbstractClass()
        ;

        $request->expects(self::any())->method('hasHeader')->with('Content-Type')->willReturn(null !== $acceptHeader);
        $request->expects(self::any())->method('getHeaderLine')->with('Content-Type')->willReturn($acceptHeader);

        return $request;
    }
}
