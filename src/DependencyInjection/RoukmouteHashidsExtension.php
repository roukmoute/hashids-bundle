<?php

declare(strict_types=1);

namespace Roukmoute\HashidsBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

class RoukmouteHashidsExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(dirname(__DIR__, 2) . '/Resources/config'));
        $loader->load('services.xml');

        foreach (['salt', 'min_hash_length', 'alphabet', 'passthrough'] as $parameter) {
            $container->setParameter('hashids.' . $parameter, $config[$parameter]);
        }
    }
}
