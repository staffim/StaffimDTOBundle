<?php
namespace Staffim\DTOBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Vyacheslav Salakhutdinov <megazoll@gmail.com>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('staffim_dto');

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
