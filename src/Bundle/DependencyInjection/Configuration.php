<?php
namespace Olla\Theme\Bundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('olla_theme', 'array');
        $rootNode
            ->children()
                ->arrayNode('themes')
                    ->useAttributeAsKey('theme')
                    ->prototype('scalar')->end()
                ->end()
                ->scalarNode('reactjs')->defaultNull()->end()
                ->scalarNode('active_theme')->defaultNull()->end()
                ->scalarNode('default_template')->defaultNull()->end()
                ->arrayNode('path_patterns')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('app_resource')
                            ->useAttributeAsKey('path')
                            ->prototype('scalar')->end()
                        ->end()
                        ->arrayNode('bundle_resource')
                            ->useAttributeAsKey('path')
                            ->prototype('scalar')->end()
                        ->end()
                        ->arrayNode('bundle_resource_dir')
                            ->useAttributeAsKey('path')
                            ->prototype('scalar')->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('assets')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('js')
                            ->useAttributeAsKey('path')
                            ->prototype('scalar')->end()
                        ->end()
                        ->arrayNode('css')
                            ->useAttributeAsKey('path')
                            ->prototype('scalar')->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
        return $treeBuilder;
    }
}
