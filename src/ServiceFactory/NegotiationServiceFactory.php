<?php

declare(strict_types=1);

namespace Chubbyphp\Negotiation\ServiceFactory;

use Chubbyphp\Negotiation\AcceptLanguageNegotiator;
use Chubbyphp\Negotiation\AcceptLanguageNegotiatorInterface;
use Chubbyphp\Negotiation\AcceptNegotiator;
use Chubbyphp\Negotiation\AcceptNegotiatorInterface;
use Chubbyphp\Negotiation\ContentTypeNegotiator;
use Chubbyphp\Negotiation\ContentTypeNegotiatorInterface;
use Chubbyphp\Negotiation\Middleware\AcceptLanguageMiddleware;
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
            'negotiator.acceptLanguageMiddleware' => static function (ContainerInterface $container): AcceptLanguageMiddleware {
                /** @var AcceptLanguageNegotiatorInterface $acceptLanguageNegotiator */
                $acceptLanguageNegotiator = $container->get('negotiator.acceptLanguageNegotiator');

                return new AcceptLanguageMiddleware($acceptLanguageNegotiator);
            },
            'negotiator.acceptLanguageNegotiator' => static function (ContainerInterface $container): AcceptLanguageNegotiator {
                /** @var array<int, string> $supportedLocales */
                $supportedLocales = $container->get('negotiator.acceptLanguageNegotiator.values');

                return new AcceptLanguageNegotiator($supportedLocales);
            },
            'negotiator.acceptMiddleware' => static function (ContainerInterface $container): AcceptMiddleware {
                /** @var AcceptNegotiatorInterface $acceptNegotiator */
                $acceptNegotiator = $container->get('negotiator.acceptNegotiator');

                return new AcceptMiddleware($acceptNegotiator);
            },
            'negotiator.acceptNegotiator' => static function (ContainerInterface $container): AcceptNegotiator {
                /** @var array<int, string> $supportedMediaTypes */
                $supportedMediaTypes = $container->get('negotiator.acceptNegotiator.values');

                return new AcceptNegotiator($supportedMediaTypes);
            },
            'negotiator.contentTypeMiddleware' => static function (ContainerInterface $container): ContentTypeMiddleware {
                /** @var ContentTypeNegotiatorInterface $contentTypeNegotiator */
                $contentTypeNegotiator = $container->get('negotiator.contentTypeNegotiator');

                return new ContentTypeMiddleware($contentTypeNegotiator);
            },
            'negotiator.contentTypeNegotiator' => static function (ContainerInterface $container): ContentTypeNegotiator {
                /** @var array<int, string> $supportedMediaTypes */
                $supportedMediaTypes = $container->get('negotiator.contentTypeNegotiator.values');

                return new ContentTypeNegotiator($supportedMediaTypes);
            },
            'negotiator.acceptNegotiator.values' => static fn (): array => [],
            'negotiator.acceptLanguageNegotiator.values' => static fn (): array => [],
            'negotiator.contentTypeNegotiator.values' => static fn (): array => [],
        ];
    }
}
