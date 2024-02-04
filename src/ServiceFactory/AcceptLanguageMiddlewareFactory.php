<?php

declare(strict_types=1);

namespace Chubbyphp\Negotiation\ServiceFactory;

use Chubbyphp\Laminas\Config\Factory\AbstractFactory;
use Chubbyphp\Negotiation\AcceptLanguageNegotiatorInterface;
use Chubbyphp\Negotiation\Middleware\AcceptLanguageMiddleware;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;

final class AcceptLanguageMiddlewareFactory extends AbstractFactory
{
    public function __invoke(ContainerInterface $container): MiddlewareInterface
    {
        return new AcceptLanguageMiddleware(
            $container->get(AcceptLanguageNegotiatorInterface::class.$this->name)
        );
    }
}
