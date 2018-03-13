<?php
namespace Olla\Theme;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment as TwigEnvironment;

final class Theme implements ThemeInterface {

    protected $activeTheme;
    protected $twig;
    protected $assets;
    protected $template;
    protected $theme;
    protected $requestStack;

	public function __construct(ActiveTheme $activeTheme, RequestStack $requestStack, TwigEnvironment $twig, array $assets = [], string $template) {
        $this->activeTheme = $activeTheme;
        $this->requestStack = $requestStack;
        $this->twig = $twig;
        $this->assets = $assets;
        $this->template = $template;
	}

    public function name() {
        
    }

    public function paths():array {
        return [];
    }
    public function assets():array {
        return $this->assets;
    }


    public function render(string $template = null, array $props = [], array $assets = [], array $react = [], array $options = [], array $contexts = []) {
        if(!$template) {
            $template  = $this->template;
        }
        $payload = [
            'context' => array_merge([
                'resource' => null,
                'operation' => null,
            ], $contexts, $this->contexts()),
            'react' => array_merge([
                'js' => null,
                'component' => 'app',
                'server' => false
            ],$react),
            'props' => $props,
            'options' => $options,
            'assets' => array_merge($this->assets(), $assets),
        ];
        return  Response::create($this->twig->render($template, array_merge($payload, $props)));
    }

    public function contexts()
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

