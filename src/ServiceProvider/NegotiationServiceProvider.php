<?php

declare(strict_types=1);

namespace Chubbyphp\Negotiation\ServiceProvider;

use Chubbyphp\Negotiation\AcceptLanguageNegotiator;
use Chubbyphp\Negotiation\AcceptLanguageNegotiatorInterface;
use Chubbyphp\Negotiation\AcceptNegotiator;
use Chubbyphp\Negotiation\AcceptNegotiatorInterface;
use Chubbyphp\Negotiation\ContentTypeNegotiator;
use Chubbyphp\Negotiation\ContentTypeNegotiatorInterface;
use Chubbyphp\Negotiation\Middleware\AcceptLanguageMiddleware;
use Chubbyphp\Negotiation\Middleware\AcceptMiddleware;
use Chubbyphp\Negotiation\Middleware\ContentTypeMiddleware;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

final class NegotiationServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container): void
    {
        $container['negotiator.acceptNegotiator'] = static function () use ($container): AcceptNegotiator {
            /** @var array<int, string> $supportedMediaTypes */
            $supportedMediaTypes = $container['negotiator.acceptNegotiator.values'];

            return new AcceptNegotiator($supportedMediaTypes);
        };

        $container['negotiator.acceptMiddleware'] = static function () use ($container): AcceptMiddleware {
            /** @var AcceptNegotiatorInterface $acceptNegotiator */
            $acceptNegotiator = $container['negotiator.acceptNegotiator'];

            return new AcceptMiddleware($acceptNegotiator);
        };

        $container['negotiator.acceptLanguageNegotiator'] = static function () use ($container): AcceptLanguageNegotiator {
            /** @var array<int, string> $supportedLocales */
            $supportedLocales = $container['negotiator.acceptLanguageNegotiator.values'];

            return new AcceptLanguageNegotiator($supportedLocales);
        };

        $container['negotiator.acceptLanguageMiddleware'] = static function () use ($container): AcceptLanguageMiddleware {
            /** @var AcceptLanguageNegotiatorInterface $acceptLanguageNegotiator */
            $acceptLanguageNegotiator = $container['negotiator.acceptLanguageNegotiator'];

            return new AcceptLanguageMiddleware($acceptLanguageNegotiator);
        };

        $container['negotiator.contentTypeNegotiator'] = static function () use ($container): ContentTypeNegotiator {
            /** @var array<int, string> $supportedMediaTypes */
            $supportedMediaTypes = $container['negotiator.contentTypeNegotiator.values'];

            return new ContentTypeNegotiator($supportedMediaTypes);
        };

        $container['negotiator.contentTypeMiddleware'] = static function () use ($container): ContentTypeMiddleware {
            /** @var ContentTypeNegotiatorInterface $contentTypeNegotiator */
            $contentTypeNegotiator = $container['negotiator.contentTypeNegotiator'];

            return new ContentTypeMiddleware($contentTypeNegotiator);
        };

        /** @return array<int, string> */
        $container['negotiator.acceptNegotiator.values'] = static fn (): array => [];

        /** @return array<int, string> */
        $container['negotiator.acceptLanguageNegotiator.values'] = static fn (): array => [];

        /** @return array<int, string> */
        $container['negotiator.contentTypeNegotiator.values'] = static fn (): array => [];
    }
}
