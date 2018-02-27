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
        $this->reconfig($configs, $container);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('theme.xml');
        $loader->load('twig.xml');
    }
    private function reconfig(array $configs, ContainerBuilder $container) {

        $config = $this->processConfiguration(new Configuration(), $configs);
        foreach (array('themes', 'active_theme', 'path_patterns', 'cache_warming') as $key) {
            $container->setParameter($this->getAlias().'.'.$key, $config[$key]);
        }
        
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $options = null;
        if (!empty($config['cookie']['name'])) {
            $options = array();
            foreach (array('name', 'lifetime', 'path', 'domain', 'secure', 'http_only') as $key) {
                $options[$key] = $config['cookie'][$key];
            }
        }

        $container->setParameter($this->getAlias().'.cookie', $options);
       
        if (!empty($config['device_detection'])) {
            $container->setAlias('liip_theme.theme_auto_detect', $config['device_detection']);
        }
        if (!empty($config['autodetect_theme'])) {
            $id = is_string($config['autodetect_theme']) ? $config['autodetect_theme'] : 'liip_theme.theme_auto_detect';
            $container->getDefinition($this->getAlias().'.theme_request_listener')
            ->addArgument(new Reference($id));
        }
        if (true === $config['assetic_integration']) {
            $container->setParameter('liip_theme.assetic_integration', true);
        }
    }
}
