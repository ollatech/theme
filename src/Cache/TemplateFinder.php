<?php
namespace Olla\Theme\Cache;

use Symfony\Bundle\FrameworkBundle\CacheWarmer\TemplateFinder as BaseTemplateFinder;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Templating\TemplateNameParserInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\Finder\Finder;
use Olla\Theme\ActiveTheme;


class TemplateFinder extends BaseTemplateFinder
{

    private $kernel;
    private $parser;
    private $rootDir;
    private $templates;
    private $theme;
    public function __construct(
        KernelInterface $kernel,
        TemplateNameParserInterface $parser,
        $rootDir,
        ActiveTheme $theme
    ) {
        parent::__construct($kernel, $parser, $rootDir);
        $this->kernel = $kernel;
        $this->parser = $parser;
        $this->rootDir = $rootDir;
        $this->theme = $theme;
    }

    /**
     * {@inheritdoc}
     */
    public function findAllTemplates()
    {
       
        $themes = $this->theme->getThemes();
        if (null !== $this->templates) {
            return $this->templates;
        }

        $templates = parent::findAllTemplates();
        foreach ($this->kernel->getBundles() as $bundle) {
            foreach ($themes as $theme) {
                $templates = array_merge($templates, $this->findTemplatesInThemes($bundle, $theme));
            }
        }

        return $this->templates = $templates;
    }

    /**
     * Find templates in the given bundle.
     *
     * @param BundleInterface $bundle The bundle where to look for templates
     * @param string          $theme
     *
     * @return array An array of templates of type TemplateReferenceInterface
     */
    private function findTemplatesInThemes(BundleInterface $bundle, $theme)
    {
        $templates = $this->findTemplatesInFolder($bundle->getPath().'/Resources/themes/'.$theme);
        $name = $bundle->getName();
        foreach ($templates as $i => $template) {
            $templates[$i] = $template->set('bundle', $name);
        }
        return $templates;
    }

    /**
     * Find templates in the given directory.
     *
     * @param string $dir The folder where to look for templates
     *
     * @return array An array of templates of type TemplateReferenceInterface
     */
    private function findTemplatesInFolder($dir)
    {
        $templates = array();
        if (is_dir($dir)) {
            $finder = new Finder();
            foreach ($finder->files()->followLinks()->in($dir) as $file) {
                $template = $this->parser->parse($file->getRelativePathname());
                if (false !== $template) {
                    $templates[] = $template;
                }
            }
        }
        return $templates;
    }
}
