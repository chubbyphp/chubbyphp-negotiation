<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Negotiation\Unit;

use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use Chubbyphp\Negotiation\ContentTypeNegotiator;
use Chubbyphp\Negotiation\NegotiatedValue;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

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

        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class);

        self::assertNull($negotiator->negotiate($request));
    }

    public function testWithoutHeader(): void
    {
        $negotiator = new ContentTypeNegotiator(['application/json']);

        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('hasHeader')->with('Content-Type')->willReturn(false),
        ]);

        self::assertNull($negotiator->negotiate($request));
    }

    /**
     * @dataProvider getToNegotiateHeaders
     *
     * @param ServerRequestInterface $request
     * @param array                  $supportedMediaTypes
     * @param NegotiatedValue|null   $expectedContentType
     */
    public function testNegotiate(
        ServerRequestInterface $request,
        array $supportedMediaTypes,
        NegotiatedValue $expectedContentType = null
    ): void {
        $negotiator = new ContentTypeNegotiator($supportedMediaTypes);

        self::assertEquals($expectedContentType, $negotiator->negotiate($request));
    }

    public function getToNegotiateHeaders(): array
    {
        return [
            [
                'request' => $this->getRequest(' application/xml ; charset = UTF-8 '),
                'supportedMediaTypes' => ['application/json', 'application/xml', 'application/x-yaml'],
                'expectedContentType' => new NegotiatedValue('application/xml', ['charset' => 'UTF-8']),
            ],
            [
                'request' => $this->getRequest('application/xml                 ; charset=UTF-8'),
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
            [
                'request' => $this->getRequest('application/jsonx+xml; charset=UTF-8'),
                'supportedMediaTypes' => ['application/xml'],
                'expectedContentType' => new NegotiatedValue('application/xml', ['charset' => 'UTF-8']),
            ],
            [
                'request' => $this->getRequest('application/jsonx+xml; charset=UTF-8'),
                'supportedMediaTypes' => ['application/json'],
                'expectedContentType' => null,
            ],
        ];
    }

    /**
     * @param string|null $contentType
     *
     * @return ServerRequestInterface
     */
    private function getRequest(string $contentType = null): ServerRequestInterface
    {
        if (null === $contentType) {
            return $this->getMockByCalls(ServerRequestInterface::class, [
                Call::create('hasHeader')->with('Content-Type')->willReturn(false),
            ]);
        }

        return $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('hasHeader')->with('Content-Type')->willReturn(true),
            Call::create('getHeaderLine')->with('Content-Type')->willReturn($contentType),
        ]);
    }
}
