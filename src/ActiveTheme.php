<?php

namespace Olla\Theme;

class ActiveTheme
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    private $themes;

    /**
     * @param string                          $name
     * @param array                           $themes
     */
    public function __construct($name, array $themes = array())
    {
        $this->setThemes($themes);
        if ($name) {
            $this->setName($name);
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