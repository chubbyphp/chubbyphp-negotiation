<?php

declare(strict_types=1);

namespace Chubbyphp\Negotiation\Middleware;

use Chubbyphp\HttpException\HttpException;
use Chubbyphp\Negotiation\ContentTypeNegotiatorInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class ContentTypeMiddleware implements MiddlewareInterface
{
    public function __construct(
        private ContentTypeNegotiatorInterface $contentTypeNegotiator,
    ) {}

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (null === $contentType = $this->contentTypeNegotiator->negotiate($request)) {
            throw HttpException::createUnsupportedMediaType(
                $this->aggregateData(
                    'content-type',
                    $request->getHeaderLine('Content-Type'),
                    $this->contentTypeNegotiator->getSupportedMediaTypes()
                )
            );
        }

        $request = $request->withAttribute('contentType', $contentType->getValue());

        return $handler->handle($request);
    }

    /**
     * @param array<string> $supportedValues
     *
     * @return array<string, array<string>|string>
     */
    private function aggregateData(string $header, string $value, array $supportedValues): array
    {
        return [
            'detail' => sprintf(
                '%s %s, supportedValues: "%s"',
                '' !== $value ? 'Not supported' : 'Missing',
                $header,
                implode('", ', $supportedValues)
            ),
            'value' => $value,
            'supportedValues' => $supportedValues,
        ];
    }
}
