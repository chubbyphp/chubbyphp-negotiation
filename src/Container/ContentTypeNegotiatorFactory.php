<?php

declare(strict_types=1);

namespace Chubbyphp\Negotiation\Container;

use Chubbyphp\Negotiation\ContentTypeNegotiator;
use Chubbyphp\Negotiation\ContentTypeNegotiatorInterface;
use Psr\Container\ContainerInterface;

final class ContentTypeNegotiatorFactory
{
    public function __invoke(ContainerInterface $container): ContentTypeNegotiatorInterface
    {
        return new ContentTypeNegotiator(
            $container->get(ContentTypeNegotiatorInterface::class.'supportedMediaTypes[]')
        );
    }
}
