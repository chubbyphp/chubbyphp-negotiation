<?php

declare(strict_types=1);

namespace Chubbyphp\Negotiation\ServiceFactory;

use Chubbyphp\Laminas\Config\Factory\AbstractFactory;
use Chubbyphp\Negotiation\AcceptNegotiatorInterface;
use Chubbyphp\Negotiation\Middleware\AcceptMiddleware;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;

final class AcceptMiddlewareFactory extends AbstractFactory
{
    public function __invoke(ContainerInterface $container): MiddlewareInterface
    {
        return new AcceptMiddleware(
            $container->get(AcceptNegotiatorInterface::class.$this->name)
        );
    }
}
