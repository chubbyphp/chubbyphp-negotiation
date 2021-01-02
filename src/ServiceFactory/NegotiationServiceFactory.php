<?php

declare(strict_types=1);

namespace Chubbyphp\Negotiation\ServiceFactory;

use Chubbyphp\Negotiation\AcceptLanguageNegotiator;
use Chubbyphp\Negotiation\AcceptNegotiator;
use Chubbyphp\Negotiation\ContentTypeNegotiator;
use Psr\Container\ContainerInterface;

final class NegotiationServiceFactory
{
    /**
     * @return array<string, callable>
     */
    public function __invoke(): array
    {
        return [
            'negotiator.acceptNegotiator' => static fn (ContainerInterface $container) => new AcceptNegotiator($container->get('negotiator.acceptNegotiator.values')),
            'negotiator.acceptLanguageNegotiator' => static fn (ContainerInterface $container) => new AcceptLanguageNegotiator($container->get('negotiator.acceptLanguageNegotiator.values')),
            'negotiator.contentTypeNegotiator' => static fn (ContainerInterface $container) => new ContentTypeNegotiator($container->get('negotiator.contentTypeNegotiator.values')),
            'negotiator.acceptNegotiator.values' => static fn () => [],
            'negotiator.acceptLanguageNegotiator.values' => static fn () => [],
            'negotiator.contentTypeNegotiator.values' => static fn () => [],
        ];
    }
}
