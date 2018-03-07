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
        $fileSystemDefinition = $container->getDefinition('twig.loader.filesystem');
        $fileSystemDefinition->setClass($container->getParameter('olla_theme.filesystem_loader.class'));
        $fileSystemDefinition->addMethodCall('setTheme', array(new Reference('olla_theme.theme')));


		if (!$container->hasDefinition('assetic.asset_manager')) {
			return;
		}


		$engines = $container->getParameter('templating.engines');
        // bundle and kernel resources
		$bundles = $container->getParameter('kernel.bundles');
		$asseticBundles = $container->getParameterBag()->resolveValue($container->getParameter('assetic.bundles'));
		
		foreach ($asseticBundles as $bundleName) {
			$rc = new \ReflectionClass($bundles[$bundleName]);
			foreach ($engines as $engine) {
				$this->setBundleDirectoryResources($container, $engine, dirname($rc->getFileName()), $bundleName);
			}
		}

		foreach ($engines as $engine) {
			$this->setAppDirectoryResources($container, $engine);
		}
	}

	protected function setBundleDirectoryResources(ContainerBuilder $container, $engine, $bundleDirName, $bundleName)
	{
		$resources = $container->getDefinition('assetic.'.$engine.'_directory_resource.'.$bundleName)->getArgument(0);
		$themes = $container->getParameter('olla_theme.themes');
		foreach ($themes as $theme) {
			$resources[] = new DirectoryResourceDefinition(
				$bundleName,
				$engine,
				array($container->getParameter('kernel.project_dir').'/templates/'.$bundleName.'/themes/'.$theme,
					$bundleDirName.'/templates/themes/'.$theme,
				)
			);
		}

		$container->getDefinition('assetic.'.$engine.'_directory_resource.'.$bundleName)->replaceArgument(0, $resources);
	}

	protected function setAppDirectoryResources(ContainerBuilder $container, $engine)
	{
		$themes = $container->getParameter('olla_theme.themes');
		foreach ($themes as $key => $theme) {
			$themes[$key] = $container->getParameter('kernel.project_dir').'/templates/themes/'.$theme;
		}
		$themes[] = $container->getParameter('kernel.project_dir').'/templates/views';

		$container->setDefinition(
			'assetic.'.$engine.'_directory_resource.kernel',
			new DirectoryResourceDefinition('', $engine, $themes)
		);
	}
}
