<?php

namespace Olla\Theme\Locator;

use Symfony\Bundle\FrameworkBundle\Templating\Loader\TemplateLocator as BaseTemplateLocator;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Templating\TemplateReferenceInterface;


class TemplateLocator extends BaseTemplateLocator
{

    public function __construct(FileLocatorInterface $locator, $cacheDir = null)
    {
        parent::__construct($locator, $cacheDir);
    }

    public function getLocator()
    {
        return $this->locator;
    }


    protected function getCacheKey($template)
    {
        $name = $template->getLogicalName();
        return $name;
    }


    public function locate($template, $currentPath = null, $first = true)
    {
  
        if (!$template instanceof TemplateReferenceInterface) {
            throw new \InvalidArgumentException('The template must be an instance of TemplateReferenceInterface.');
        }
        $key = $this->getCacheKey($template);
        if (!isset($this->cache[$key])) {
            try {
              
                $this->cache[$key] = $this->locator->locate($template->getPath(), $currentPath);
            } catch (\InvalidArgumentException $e) {
                throw new \InvalidArgumentException(sprintf('Unable to find template "%s" in "%s".', $template, $e->getMessage()), 0, $e);
            }
        }
        return $this->cache[$key];
    }
}