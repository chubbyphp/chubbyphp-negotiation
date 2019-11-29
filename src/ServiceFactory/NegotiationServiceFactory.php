<?php

declare(strict_types=1);

namespace Chubbyphp\Negotiation\ServiceFactory;

use Chubbyphp\Negotiation\AcceptLanguageNegotiator;
use Chubbyphp\Negotiation\AcceptNegotiator;
use Chubbyphp\Negotiation\ContentTypeNegotiator;
use Psr\Container\ContainerInterface;

final class NegotiationServiceFactory
{
    public function __invoke(): array
    {
        return [
            'negotiator.acceptNegotiator' => static function (ContainerInterface $container) {
                return new AcceptNegotiator($container->get('negotiator.acceptNegotiator.values'));
            },
            'negotiator.acceptLanguageNegotiator' => static function (ContainerInterface $container) {
                return new AcceptLanguageNegotiator($container->get('negotiator.acceptLanguageNegotiator.values'));
            },
            'negotiator.contentTypeNegotiator' => static function (ContainerInterface $container) {
                return new ContentTypeNegotiator($container->get('negotiator.contentTypeNegotiator.values'));
            },
            'negotiator.acceptNegotiator.values' => static function () {
                return [];
            },
            'negotiator.acceptLanguageNegotiator.values' => static function () {
                return [];
            },
            'negotiator.contentTypeNegotiator.values' => static function () {
                return [];
            },
        ];
    }
}
