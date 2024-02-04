<?php

declare(strict_types=1);

namespace Chubbyphp\Negotiation\ServiceFactory;

use Chubbyphp\Negotiation\AcceptLanguageNegotiator;
use Chubbyphp\Negotiation\AcceptNegotiator;
use Chubbyphp\Negotiation\ContentTypeNegotiator;
use Chubbyphp\Negotiation\Middleware\AcceptMiddleware;
use Chubbyphp\Negotiation\Middleware\ContentTypeMiddleware;
use Psr\Container\ContainerInterface;

final class NegotiationServiceFactory
{
    /**
     * @return array<string, callable>
     */
    public function __invoke(): array
    {
        return [
            'negotiator.acceptLanguageMiddleware' => static fn (ContainerInterface $container) => new AcceptLanguageNegotiator($container->get('negotiator.acceptLanguageNegotiator')),
            'negotiator.acceptLanguageNegotiator' => static fn (ContainerInterface $container) => new AcceptLanguageNegotiator($container->get('negotiator.acceptLanguageNegotiator.values')),
            'negotiator.acceptMiddleware' => static fn (ContainerInterface $container) => new AcceptMiddleware($container->get('negotiator.acceptNegotiator')),
            'negotiator.acceptNegotiator' => static fn (ContainerInterface $container) => new AcceptNegotiator($container->get('negotiator.acceptNegotiator.values')),
            'negotiator.contentTypeMiddleware' => static fn (ContainerInterface $container) => new ContentTypeMiddleware($container->get('negotiator.contentTypeNegotiator')),
            'negotiator.contentTypeNegotiator' => static fn (ContainerInterface $container) => new ContentTypeNegotiator($container->get('negotiator.contentTypeNegotiator.values')),
            'negotiator.acceptNegotiator.values' => static fn () => [],
            'negotiator.acceptLanguageNegotiator.values' => static fn () => [],
            'negotiator.contentTypeNegotiator.values' => static fn () => [],
        ];
    }
}
