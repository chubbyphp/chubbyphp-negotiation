<?php

declare(strict_types=1);

namespace Chubbyphp\Negotiation\Provider;

use Chubbyphp\Negotiation\ServiceProvider\NegotiationServiceProvider;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

final class NegotiationProvider implements ServiceProviderInterface
{
    /**
     * @var NegotiationServiceProvider
     */
    private $serviceProvider;

    public function __construct()
    {
        $this->serviceProvider = new NegotiationServiceProvider();
    }

    public function register(Container $container): void
    {
        $this->serviceProvider->register($container);
    }
}
