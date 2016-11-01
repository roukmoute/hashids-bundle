<?php
namespace Roukmoute\HashidsBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

class RoukmouteHashidsExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../../Resources/config'));
        $loader->load('services.xml');

        $container->setParameter('hashids.salt', $config['salt']);
        $container->setParameter('hashids.min_hash_length', $config['min_hash_length']);
        $container->setParameter('hashids.alphabet', $config['alphabet']);
        $container->setParameter('hashids.autowire', $config['autowire']);
    }
}
