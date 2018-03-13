<?php
namespace Olla\Theme;

use Olla\Theme\ThemeAsset;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment as TwigEnvironment;

final class Theme implements ThemeInterface {

    protected $themeAsset;
    protected $activeTheme;
    protected $twig;
    protected $assets;
    protected $template;
    protected $theme;
    protected $requestStack;

    public function __construct(ThemeAsset $themeAsset, ActiveTheme $activeTheme, RequestStack $requestStack, TwigEnvironment $twig, array $assets = [], string $template) {
        $this->themeAsset = $themeAsset;
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

    protected function currentTheme() {
        return $this->activeTheme->theme();
    }


    public function render(string $template = null, array $props = [], array $assets = [], array $react = [], array $options = [], array $contexts = []) {

        if(null === $theme = $this->currentTheme()) {
            return;
        }

        $contexts = array_merge([
            'resource' => null,
            'operation' => null,
        ], $this->contexts(), $theme->contexts(), $contexts);

        $react = array_merge([
            'js' => null,
            'component' => 'app',
            'server' => false
        ],$react);
        $assets = array_merge($this->assets(), $theme->assets(), $assets);


        $theme_react = $this->themeAsset->react($contexts, $react);
        $theme_js = $this->themeAsset->js($contexts, $assets);
        $theme_css = $this->themeAsset->css($contexts, $assets);
        $theme_fonts = $this->themeAsset->fonts($contexts, $assets);

        if(!$template) {
            $template  = $this->template;
        }
        $payload = [
            'context' => $contexts,
            'react' => $theme_react,
            'javascripts' => $theme_js,
            'styles' => $theme_css,
            'fonts' => $theme_fonts,
            'props' => $props,
            'options' => $options
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

