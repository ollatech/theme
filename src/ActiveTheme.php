<?php

namespace Olla\Theme;

use Symfony\Component\HttpFoundation\RequestStack;
use Olla\Theme\ThemeRegistry;

class ActiveTheme
{
    protected $themeRegistry;

    protected $requestStack;
    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $themes = [];

    /**
     * @param string                          $name
     * @param array                           $themes
     */
    public function __construct(ThemeRegistry $themeRegistry, RequestStack $requestStack, $name, array $themes = array())
    {
        $this->themeRegistry = $themeRegistry;
        $this->requestStack = $requestStack;
    }



    public function theme() {
        $active_theme = $this->activeTheme();
  
        if($active_theme) {
            if($this->themeRegistry->has($active_theme)) {
                return $this->themeRegistry->get($active_theme);
            }
            throw new \InvalidArgumentException(sprintf(
                'The active theme "%s" must be in the themes list (%s)', $active_theme, implode(',', array_keys($this->themeRegistry->all()))
            ));
        }

    }
    public function activeTheme()
    {
        $request = $this->requestStack->getCurrentRequest();
        if( $request && null !== $theme = $request->attributes->get('_theme')) {
            return $theme;
        }
    }

    public function path() {
        if($this->get()) {
            return 'black_theme';
        }
    }

    public function device() {
        return 'desktop';
    }



    public function themes() {
        return $this->themeRegistry->allThemes();
    }

    public function sessionTheme()
    {
        $request = $this->requestStack->getCurrentRequest();
        if( $request && null !== $theme = $request->attributes->get('_theme')) {
            return $theme;
        }
    }

    

    public function getThemes()
    {
        return (array) $this->themes;
    }

    public function setThemes(array $themes)
    {
        $this->themes = $themes;
    }
    public function getTheme()
    {
        if(null !== $theme = $this->sessionTheme()) {
            return $theme;
        }
        return $this->name;
    }

    public function getDevice()
    {
        return;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        if (!array_key_exists($name, $this->themeRegistry->allThemes())) {

            throw new \InvalidArgumentException(sprintf(
                'The active theme "%s" must be in the themes list (%s)', $name, implode(',', array_keys($this->themeRegistry->allThemes()))
            ));
        }
        $this->name = $name;
    }
}