<?php
namespace Olla\Theme;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class OllaThemeBundle extends Bundle
{
	public function build(ContainerBuilder $container)
	{
		parent::build($container);
	
	}
}
