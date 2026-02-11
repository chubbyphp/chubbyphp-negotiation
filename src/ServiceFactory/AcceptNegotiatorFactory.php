<?php

declare(strict_types=1);

namespace Chubbyphp\Negotiation\ServiceFactory;

use Chubbyphp\Laminas\Config\Factory\AbstractFactory;
use Chubbyphp\Negotiation\AcceptNegotiator;
use Chubbyphp\Negotiation\AcceptNegotiatorInterface;
use Psr\Container\ContainerInterface;

final class AcceptNegotiatorFactory extends AbstractFactory
{
    public function __invoke(ContainerInterface $container): AcceptNegotiatorInterface
    {
        /** @var list<string> $supportedMediaTypes */
        $supportedMediaTypes = $container->get(AcceptNegotiatorInterface::class.'supportedMediaTypes[]'.$this->name);

        return new AcceptNegotiator($supportedMediaTypes);
    }
}
