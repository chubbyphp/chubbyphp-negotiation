<?php

declare(strict_types=1);

namespace Chubbyphp\Negotiation\Provider;

use Chubbyphp\Negotiation\ServiceProvider\NegotiationServiceProvider;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * @deprecated use \Chubbyphp\Negotiation\ServiceProvider\NegotiationServiceProvider
 */
final class NegotiationProvider implements ServiceProviderInterface
{
    /**
     * @var NegotiationServiceProvider
     */
    private $serviceProvider;

    public function __construct()
    {
        @trigger_error(
            sprintf('Use "%s" instead.', NegotiationServiceProvider::class),
            E_USER_DEPRECATED
        );

        $this->serviceProvider = new NegotiationServiceProvider();
    }

    public function register(Container $container): void
    {
        $this->serviceProvider->register($container);
    }
}
