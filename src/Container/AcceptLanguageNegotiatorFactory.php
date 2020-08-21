<?php

declare(strict_types=1);

namespace Chubbyphp\Negotiation\Container;

use Chubbyphp\Negotiation\AcceptLanguageNegotiator;
use Chubbyphp\Negotiation\AcceptLanguageNegotiatorInterface;
use Psr\Container\ContainerInterface;

final class AcceptLanguageNegotiatorFactory
{
    public function __invoke(ContainerInterface $container): AcceptLanguageNegotiatorInterface
    {
        return new AcceptLanguageNegotiator(
            $container->get(AcceptLanguageNegotiatorInterface::class.'supportedLocales[]')
        );
    }
}
