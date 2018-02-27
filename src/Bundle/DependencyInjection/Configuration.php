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
                ->scalarNode('active_theme')->defaultNull()->end()
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
                ->arrayNode('cookie')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('name')->cannotBeEmpty()->end()
                        ->scalarNode('lifetime')->defaultValue(31536000)->end()
                        ->scalarNode('path')->defaultValue('/')->end()
                        ->scalarNode('domain')->defaultValue('')->end()
                        ->booleanNode('secure')->defaultFalse()->end()
                        ->booleanNode('http_only')->defaultFalse()->end()
                    ->end()
                ->end()
                ->scalarNode('autodetect_theme')->defaultFalse()->end()
                ->scalarNode('device_detection')->defaultFalse()->end()
                ->booleanNode('cache_warming')->defaultTrue()->end()
                ->booleanNode('load_controllers')->defaultTrue()->end()
                ->booleanNode('assetic_integration')->defaultFalse()->end()
                ->booleanNode('theme_specific_controllers')->defaultFalse()->end()
            ->end()
        ;
        return $treeBuilder;
    }
}
