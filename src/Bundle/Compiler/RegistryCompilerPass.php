<?php

namespace Olla\Theme\Bundle\Compiler;

use Olla\Theme\ThemeInterface;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class RegistryCompilerPass implements CompilerPassInterface
{

	public function process(ContainerBuilder $container)
	{
		$registry = $container->findDefinition('olla_theme.registry');
		$taggesAction = $container->findTaggedServiceIds('olla_theme.theme', true);

		foreach ($taggesAction as $id => $tags) {
			$alias = null;
			foreach ($tags as $tag) {
				if(isset($tag['alias'])) {
					$alias = $tag['alias'];
				}
			}
			if(!$alias) {
				continue;
			}
			if ($container->hasDefinition($id)) {

				//if(new Reference($id) instanceof ThemeInterface) {
					$registry->addMethodCall(
						'addTheme', [$alias, new Reference($id)]
					);
				//}
				
			}
		}
	}
}