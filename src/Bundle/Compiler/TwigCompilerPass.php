<?php

namespace Olla\Theme\Bundle\Compiler;

use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class TwigCompilerPass implements CompilerPassInterface
{
	public function process(ContainerBuilder $container)
	{

		$container->setAlias('templating.locator', 'olla_theme.templating_locator');
        $twigSystem = $container->getDefinition('twig.loader.filesystem');
        $twigSystem->setClass($container->getParameter('olla_theme.filesystem_loader.class'));
        $twigSystem->addMethodCall('setTheme', array(new Reference('olla_theme.active_theme')));
	}

}
