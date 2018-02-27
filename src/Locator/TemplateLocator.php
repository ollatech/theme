<?php

namespace Olla\Theme\Locator;

use Symfony\Bundle\FrameworkBundle\Templating\Loader\TemplateLocator as BaseTemplateLocator;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Templating\TemplateReferenceInterface;
use Olla\Theme\ActiveTheme;

class TemplateLocator extends BaseTemplateLocator
{
    /**
     * @var ActiveTheme|null
     */
    protected $activeTheme;

    /**
     * Constructor.
     *
     * @param FileLocatorInterface $locator     A FileLocatorInterface instance
     * @param string               $cacheDir    The cache path
     * @param ActiveTheme          $activeTheme
     */
    public function __construct(FileLocatorInterface $locator, $cacheDir = null, ActiveTheme $activeTheme = null)
    {
        $this->activeTheme = $activeTheme;
        parent::__construct($locator, $cacheDir);
    }

    public function getLocator()
    {
        return $this->locator;
    }

    /**
     * Returns a full path for a given file.
     *
     * @param TemplateReferenceInterface $template A template
     *
     * @return string The full path for the file
     */
    protected function getCacheKey($template)
    {
        $name = $template->getLogicalName();

        if ($this->activeTheme) {
            $name .= '|'.$this->activeTheme->getName();
        }

        return $name;
    }

    /**
     * Returns a full path for a given file.
     *
     * @param TemplateReferenceInterface $template    A template
     * @param string                     $currentPath Unused
     * @param bool                       $first       Unused
     *
     * @return string The full path for the file
     *
     * @throws \InvalidArgumentException When the template is not an instance of TemplateReferenceInterface
     * @throws \InvalidArgumentException When the template file can not be found
     */
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