<?php

declare(strict_types=1);

namespace Chubbyphp\Negotiation\ServiceProvider;

use Chubbyphp\Negotiation\AcceptLanguageNegotiator;
use Chubbyphp\Negotiation\AcceptNegotiator;
use Chubbyphp\Negotiation\ContentTypeNegotiator;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

final class NegotiationServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container): void
    {
        $container['negotiator.acceptNegotiator'] = static fn () => new AcceptNegotiator($container['negotiator.acceptNegotiator.values']);

        $container['negotiator.acceptLanguageNegotiator'] = static fn () => new AcceptLanguageNegotiator($container['negotiator.acceptLanguageNegotiator.values']);

        $container['negotiator.contentTypeNegotiator'] = static fn () => new ContentTypeNegotiator($container['negotiator.contentTypeNegotiator.values']);

        $container['negotiator.acceptNegotiator.values'] = static fn () => [];

        $container['negotiator.acceptLanguageNegotiator.values'] = static fn () => [];

        $container['negotiator.contentTypeNegotiator.values'] = static fn () => [];
    }
}
