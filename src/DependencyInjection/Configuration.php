<?php

declare(strict_types=1);

namespace Roukmoute\HashidsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\NodeParentInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): NodeParentInterface
    {
        $treeBuilder = new TreeBuilder('roukmoute_hashids');
        $rootNode = $treeBuilder->getRootNode();

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
                ->booleanNode('passthrough')
                    ->info('if true, will continue with others param converters.')
                    ->defaultFalse()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
