<?php
namespace Olla\Theme;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Twig\Environment as TwigEnvironment;

final class View implements ThemeInterface{

    protected $twig;
	protected $context;
    protected $assets;
    protected $template;

	public function __construct(TwigEnvironment $twig, Context $context, array $assets = [], string $template) {
        $this->twig = $twig;
		$this->context = $context;
        $this->assets = $assets;
        $this->template = $template;
	}
    /**
     * metadata:
     **/
    public function render(string $template = null, array $props = [], array $assets = [], array $react = [], array $options = [], array $context = []) {
        if(!$template) {
            $template  = $this->template;
        }
        $payload = [
            'context' => array_merge([
                'resource' => null,
                'operation' => null,
            ], $context, $this->context->get()),
            'props' => $props,
            'react' => array_merge([
                'js' => null,
                'component' => 'app',
                'server' => false
            ],$react),
            'options' => $options,
            'assets' => array_merge($this->assets, $assets),
        ];
        return  Response::create($this->twig->render($template, $payload));
    }
}

