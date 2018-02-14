<?php
namespace Olla\Theme;

use Twig\Environment as TwigEnvironment;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Finder\Finder;

final class Design {
	protected $container;
	protected $twig;
	protected $finder;
	protected $designs;
	

	public function __construct(ContainerInterface $container, TwigEnvironment $twig, Finder $finder) {
		$this->container = $container;
		$this->twig = $twig;
		$this->finder = $finder;
	}

	public function view(string $design, string $template, array $data = []) {
		$templateFile = $this->template($design, $template);
		return $this->twig->render($templateFile, $data);
	}

	public function template(string $design, string $template) {

		if(!isset($this->designs[$design])) {
			throw new \Exception(sprintf('%s theme context not available', $design));
		}
		return $this->container->getParameter('kernel.project_dir').'/design/'.$design.'/'.$template;
	}

}