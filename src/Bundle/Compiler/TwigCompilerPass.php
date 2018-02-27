<?php

namespace Olla\Theme\Bungle\Compiler;

use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class TwigCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $container->setAlias('templating.locator', 'olla_theme.templating_locator');
        /*
        $container->setAlias('templating.cache_warmer.template_paths', 'liip_theme.templating.cache_warmer.template_paths');

        if (!$container->getParameter('liip_theme.cache_warming')) {
            $container->getDefinition('liip_theme.templating.cache_warmer.template_paths')
                ->replaceArgument(2, null);
        }
        */
        $twigFilesystemLoaderDefinition = $container->getDefinition('twig.loader.filesystem');
        $twigFilesystemLoaderDefinition->setClass($container->getParameter('olla_theme.filesystem_loader.class'));
        $twigFilesystemLoaderDefinition->addMethodCall('setActiveTheme', array(new Reference('olla_theme.active_theme')));
    }
}
