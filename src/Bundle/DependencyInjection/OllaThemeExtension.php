<?php

namespace Olla\Theme\Bundle\DependencyInjection;


use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Theme\DirectoryTheme;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Yaml\Yaml;


final class OllaThemeExtension extends Extension implements PrependExtensionInterface
{
    public function prepend(ContainerBuilder $container)
    {

    }
    public function load(array $configs, ContainerBuilder $container)
    {
     
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('theme.xml');
        $loader->load('registry.xml');
        $this->reconfig($configs, $container);
    }
    private function reconfig(array $configs, ContainerBuilder $container) {

        $config = $this->processConfiguration(new Configuration(), $configs);
        foreach (array('path_patterns') as $key) {
           $container->setParameter($this->getAlias().'.'.$key, $config[$key]);
        }
        //themes
        $themes = ['default'];
        if($config['themes']) {
            $themes = array_merge($themes, $config['themes']);
        }
        $container->setParameter('olla_theme.themes', $themes);
        //active theme
        $active_theme = 'default';
        if($config['active_theme']) {
            $active_theme = $config['active_theme'];
        }
        $container->setParameter('olla_theme.active_theme', $active_theme);
        //js & css
        $assets = [];
        if($config['assets']) {
            $js = [];
            $css = [];
            $rawAssets = $config['assets'];
            if(isset($rawAssets['js'])) {
                $js = $rawAssets['js'];
            }
            if(isset($rawAssets['css'])) {
                $css = $rawAssets['css'];
            }
            $assets = array_merge($assets, [
                'js' => $js,
                'css' => $css
            ]);
        }
        $container->setParameter('olla_theme.assets', $assets);
        $default_template = '@OllaTheme/app.html.twig';
        if($config['default_template']) {
            $default_template = $config['default_template'];
        }
        $container->setParameter('olla_theme.default_template', $default_template);
    }
}
