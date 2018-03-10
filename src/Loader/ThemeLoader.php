<?php

namespace Olla\Theme\Loader;

use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Templating\TemplateNameParserInterface;
use Symfony\Component\Templating\TemplateReferenceInterface;
use Olla\Theme\ActiveTheme;

class ThemeLoader extends \Twig_Loader_Filesystem
{
    protected $locator;
    protected $parser;
    protected $theme;

    public function __construct(FileLocatorInterface $locator, TemplateNameParserInterface $parser, $rootPath = null)
    {
        parent::__construct(array(), $rootPath);
        $this->locator = $locator;
        $this->parser = $parser;
    }

    public function setTheme(ActiveTheme $theme) {
        $this->theme = $theme;
    }

    protected function findTemplate($template, $throw = true)
    {
        $logicalName = (string) $template;
        if ($this->theme) {
            $logicalName .= '|'.$this->theme->getTheme();
        }
        if (isset($this->cache[$logicalName])) {
            return $this->cache[$logicalName];
        }

        $file = null;
        $previous = null;

        try {
            $templateReference = $this->parser->parse($template);
            $file = $this->locator->locate($templateReference);
        } catch (\Exception $e) {
            $previous = $e;
            try {
                $file = parent::findTemplate((string) $template);
            } catch (\Twig_Error_Loader $e) {
                $previous = $e;
            }
        }

        if (false === $file || null === $file) {
            if ($throw) {
                throw new \Twig_Error_Loader(sprintf('Unable to find template "%s".', $logicalName), -1, null, $previous);
            }
            
            return false;
        }
        return $this->cache[$logicalName] = $file;
    }
}
