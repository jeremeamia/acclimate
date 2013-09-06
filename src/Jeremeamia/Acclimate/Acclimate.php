<?php

namespace Jeremeamia\Acclimate;

use Jeremeamia\Acclimate\Adapter\ArrayContainerAdapter;

/**
 * The Acclimate class is used to acclimate a container into your code. It is essentially a factory class for the
 * container adapters in the Acclimate package.
 */
class Acclimate
{
    /**
     * @var array Map of container classes to container adapter class
     */
    private $adapterMap = array(
        'Aura\Di\ContainerInterface' => 'Jeremeamia\Acclimate\Adapter\AuraContainerAdapter',
        'Guzzle\Service\Builder\ServiceBuilderInterface' => 'Jeremeamia\Acclimate\Adapter\GuzzleContainerAdapter',
        'Illuminate\Container\Container' => 'Jeremeamia\Acclimate\Adapter\LaravelContainerAdapter',
        'Pimple' => 'Jeremeamia\Acclimate\Adapter\PimpleContainerAdapter',
        'Symfony\Component\DependencyInjection\ContainerInterface' => 'Jeremeamia\Acclimate\Adapter\SymfonyContainerAdapter',
        'Zend\Di\LocatorInterface' => 'Jeremeamia\Acclimate\Adapter\ZendDiContainerAdapter',
        'Zend\ServiceManager\ServiceLocatorInterface' => 'Jeremeamia\Acclimate\Adapter\ZendServiceManagerContainerAdapter',
    );

    /**
     * @param array $adapterMap
     */
    public function __construct(array $adapterMap = array())
    {
        $this->adapterMap = $adapterMap + $this->adapterMap;
    }

    /**
     * @param string $adapterFqcn   The fully qualified class name of the container adapter
     * @param string $containerFqcn The fully qualified class name of the container
     *
     * @return self
     */
    public function registerAdapter($adapterFqcn, $containerFqcn)
    {
        $this->adapterMap[$containerFqcn] = $adapterFqcn;

        return $this;
    }

    /**
     * @param object $container
     *
     * @return ContainerInterface
     * @throws AdapterNotFoundException
     */
    public function adaptContainer($container)
    {
        if ($container instanceof ContainerInterface) {
            return $container;
        } else {
            foreach ($this->adapterMap as $containerFqcn => $adapterFqcn) {
                if ($container instanceof $containerFqcn) {
                    return new $adapterFqcn($container);
                }
            }
            if ($container instanceof \ArrayAccess) {
                return new ArrayContainerAdapter($container);
            } else {
                throw AdapterNotFoundException::fromContainer($container);
            }

        }
    }
}
