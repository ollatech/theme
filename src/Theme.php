<?php

namespace Olla\Theme;

use Symfony\Component\HttpFoundation\RequestStack;

class Theme
{
    protected $requestStack;
    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $themes;

    /**
     * @param string                          $name
     * @param array                           $themes
     */
    public function __construct(RequestStack $requestStack, $name, array $themes = array())
    {
        $this->requestStack = $requestStack;
        $this->setThemes($themes);
        if ($name) {
            $this->setName($name);
        }
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
        if (!in_array($name, $this->themes)) {
            throw new \InvalidArgumentException(sprintf(
                'The active theme "%s" must be in the themes list (%s)',
                $name, implode(',', $this->themes)
            ));
        }
        $this->name = $name;
    }
}