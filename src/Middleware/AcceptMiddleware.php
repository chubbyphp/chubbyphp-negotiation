<?php

declare(strict_types=1);

namespace Chubbyphp\Negotiation\Middleware;

use Chubbyphp\HttpException\HttpException;
use Chubbyphp\Negotiation\AcceptNegotiatorInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class AcceptMiddleware implements MiddlewareInterface
{
    public function __construct(
        private AcceptNegotiatorInterface $acceptNegotiator,
    ) {}

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (null === $accept = $this->acceptNegotiator->negotiate($request)) {
            throw HttpException::createNotAcceptable(
                $this->aggregateData(
                    'accept',
                    $request->getHeaderLine('Accept'),
                    $this->acceptNegotiator->getSupportedMediaTypes()
                )
            );
        }

        $request = $request->withAttribute('accept', $accept->getValue());

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
