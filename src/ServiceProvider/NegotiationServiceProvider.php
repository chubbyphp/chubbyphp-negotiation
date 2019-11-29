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
        $container['negotiator.acceptNegotiator'] = static function () use ($container) {
            return new AcceptNegotiator($container['negotiator.acceptNegotiator.values']);
        };

        $container['negotiator.acceptLanguageNegotiator'] = static function () use ($container) {
            return new AcceptLanguageNegotiator($container['negotiator.acceptLanguageNegotiator.values']);
        };

        $container['negotiator.contentTypeNegotiator'] = static function () use ($container) {
            return new ContentTypeNegotiator($container['negotiator.contentTypeNegotiator.values']);
        };

        $container['negotiator.acceptNegotiator.values'] = static function () {
            return [];
        };

        $container['negotiator.acceptLanguageNegotiator.values'] = static function () {
            return [];
        };

        $container['negotiator.contentTypeNegotiator.values'] = static function () {
            return [];
        };
    }
}
