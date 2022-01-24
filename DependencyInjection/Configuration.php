<?php
namespace Staffim\DTOBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Vyacheslav Salakhutdinov <megazoll@gmail.com>
 */
class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('staffim_dto');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->scalarNode('dto_namespace')
                ->end()
                ->scalarNode('dto_postfix')
                    ->defaultValue('DTO')
                ->end()
                ->scalarNode('trigger_events')
                    ->defaultValue(true)
                ->end()
                ->scalarNode('cache')
                    ->defaultValue(true)
                ->end()
                ->arrayNode('default_mapping')
                    ->normalizeKeys(false)
                    ->useAttributeAsKey('key')
                    ->prototype('array')
                        ->children()
                            ->arrayNode('hideFields')
                                ->prototype('scalar')->end()
                            ->end()
                            ->arrayNode('fields')
                                ->prototype('scalar')->end()
                            ->end()
                            ->arrayNode('relations')
                                ->prototype('scalar')->end()
                            ->end()
                        ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
