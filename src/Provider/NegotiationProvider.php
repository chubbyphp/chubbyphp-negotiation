<?php

declare(strict_types=1);

namespace Chubbyphp\Negotiation\Provider;

use Chubbyphp\Negotiation\AcceptLanguageNegotiator;
use Chubbyphp\Negotiation\AcceptNegotiator;
use Chubbyphp\Negotiation\ContentTypeNegotiator;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

final class NegotiationProvider implements ServiceProviderInterface
{
    /**
     * @param Container $container
     */
    public function register(Container $container)
    {
        $container['negotiator.acceptNegotiator'] = function () use ($container) {
            return new AcceptNegotiator($container['negotiator.acceptNegotiator.values']);
        };

        $container['negotiator.acceptLanguageNegotiator'] = function () use ($container) {
            return new AcceptLanguageNegotiator($container['negotiator.acceptLanguageNegotiator.values']);
        };

        $container['negotiator.contentTypeNegotiator'] = function () use ($container) {
            return new ContentTypeNegotiator($container['negotiator.contentTypeNegotiator.values']);
        };

        $container['negotiator.acceptNegotiator.values'] = function () {
            return [];
        };

        $container['negotiator.acceptLanguageNegotiator.values'] = function () {
            return [];
        };

        $container['negotiator.contentTypeNegotiator.values'] = function () {
            return [];
        };
    }
}
