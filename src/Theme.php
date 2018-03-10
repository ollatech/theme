<?php
namespace Olla\Theme;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment as TwigEnvironment;

final class Theme implements ThemeInterface {

    protected $twig;
    protected $assets;
    protected $template;
    protected $theme;
    protected $requestStack;

	public function __construct(RequestStack $requestStack, TwigEnvironment $twig, array $assets = [], string $template) {
        $this->requestStack = $requestStack;
        $this->twig = $twig;
        $this->assets = $assets;
        $this->template = $template;
	}

    public function setTheme($theme) {
        $this->theme = $theme;
        return $this;
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
            ], $context, $this->context()),
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

    public function context()
    {
        $request = $this->requestStack->getCurrentRequest();
        return [
            'href' => $request->getSchemeAndHttpHost().$request->getRequestUri(),
            'location' => $request->getRequestUri(),
            'scheme' => $request->getScheme(),
            'host' => $request->getHost(),
            'port' => $request->getPort(),
            'base' => $request->getBaseUrl(),
            'pathname' => $request->getPathInfo(),
            'search' => $request->getQueryString(),
        ];
    }
}

