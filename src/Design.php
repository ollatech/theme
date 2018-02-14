<?php
namespace Olla\Theme;

use Twig\Environment as TwigEnvironment;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Finder\Finder;

final class Design {

	protected $theme  = 'default';
	protected $container;
	protected $twig;
	protected $finder;
	protected $designs;
	

	public function __construct(ContainerInterface $container, TwigEnvironment $twig, Finder $finder) {
		$this->container = $container;
		$this->twig = $twig;
		$this->finder = $finder;
	}

	public function get(string $carrier, string $template) {
		$design = $this->theme;
		return $this->container->getParameter('kernel.project_dir').'/design/'.$carrier.'/'.$design.'/'.$template;
	}
}