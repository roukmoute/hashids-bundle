<?php
namespace Roukmoute\HashidsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('roukmoute_hashids');

        $rootNode
            ->children()
                ->scalarNode('salt')
                    ->defaultValue('')
                    ->info('if set, the hashids will differ from everyone else\'s.')
                ->end()
                ->integerNode('min_hash_length')
                    ->info('if set, will generate minimum length for the id.')
                    ->defaultValue(0)
                    ->min(0)
                ->end()
                ->scalarNode('alphabet')
                    ->info('if set, will use only characters of alphabet string.')
                    ->defaultValue('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890')
                ->end()
                ->booleanNode('autowire')
                    ->info('if true, will try to detect the hashids when Doctrine was unable to guess Entity.')
                    ->defaultFalse()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
