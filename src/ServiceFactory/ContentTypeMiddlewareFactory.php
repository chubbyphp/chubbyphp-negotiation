<?php

declare(strict_types=1);

namespace Chubbyphp\Negotiation\ServiceFactory;

use Chubbyphp\Laminas\Config\Factory\AbstractFactory;
use Chubbyphp\Negotiation\ContentTypeNegotiatorInterface;
use Chubbyphp\Negotiation\Middleware\ContentTypeMiddleware;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;

final class ContentTypeMiddlewareFactory extends AbstractFactory
{
    public function __invoke(ContainerInterface $container): MiddlewareInterface
    {
        return new ContentTypeMiddleware(
            $container->get(ContentTypeNegotiatorInterface::class.$this->name)
        );
    }
}
