<?php

declare(strict_types=1);

namespace Chubbyphp\Negotiation\Container;

use Chubbyphp\Negotiation\AcceptNegotiator;
use Chubbyphp\Negotiation\AcceptNegotiatorInterface;
use Psr\Container\ContainerInterface;

final class AcceptNegotiatorFactory
{
    public function __invoke(ContainerInterface $container): AcceptNegotiatorInterface
    {
        return new AcceptNegotiator($container->get(AcceptNegotiatorInterface::class.'supportedMediaTypes[]'));
    }
}
