<?php


namespace Olla\Theme\Locator;

use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Config\FileLocator as BaseFileLocator;
use Olla\Theme\ActiveTheme;

class FileLocator extends BaseFileLocator
{
    protected $kernel;
    protected $path;
    protected $basePaths = array();
    protected $pathPatterns;
    protected $activeTheme;
    protected $currentTheme;
    protected $currentDevice;

    public function __construct(
        KernelInterface $kernel,
        $path = null,
        array $paths = array(),
        array $pathPatterns = array(),
        ActiveTheme $activeTheme
    ) {
        $this->kernel = $kernel;
        $this->path = $path;
        $this->basePaths = $paths;
        $this->activeTheme = $activeTheme;

        $defaultPathPatterns = array(
            'app_resource' => array(
                '%app_path%/themes/%current_theme%/devices/%current_device%/views/%template%',
                '%app_path%/themes/%current_theme%/views/%template%',
                '%app_path%/templates/%current_theme%/devices/%current_device%/views/%template%',
                '%app_path%/templates/%current_theme%/views/%template%',
            ),
            'app_bundle_resource' => array(
                '%app_path%/themes/%current_theme%/%bundle_name%/devices/%current_device%/views/%template%',
                '%app_path%/themes/%current_theme%/%bundle_name%/views/%template%',
                '%app_path%/templates/%current_theme%/%bundle_name%/devices/%current_device%/views/%template%',
                '%app_path%/templates/%current_theme%/%bundle_name%/views/%template%',
            ),
            'bundle_resource' => array(
                '%bundle_path%/Resources/views/devices/%current_device%/%template%',
                '%bundle_path%/Resources/views/%template%'
            )
        );
        $this->pathPatterns = array_merge_recursive(array_filter($pathPatterns), $defaultPathPatterns);
        parent::__construct(array());
    }

    protected function currentTheme() {
        return $this->activeTheme->theme();
    }

    protected function pathPatterns() {
        $currentTheme = $this->activeTheme->get();
        return array_merge_recursive($this->pathPatterns, $currentTheme->paths());
    }
    public function locate($name, $dir = null, $first = true)
    {

        if(null !== $theme = $this->currentTheme()) {
            if(null !== $template = $this->locateAppTemplate($theme, $name, $dir, $first)) {
                return $template;
            }
        }
        if ('@' === $name[0]) {
            if(null !== $template = $this->locateBundleTemplate($name, $dir, $first)) {
                return $template;
            }
        }
        

        return parent::locate($name, $dir, $first);
    }

    public function locateAppTemplate($theme, $template, $dir = null, $first = true) {
        if (0 === strpos($template, 'views/')) {
            $patterns[1] = '/Resources\//';
            $patterns[2] = '/Templates\//';
            $patterns[3] = '/views\//';
            $replacements = array();
            $replacements[3] = '';
            $replacements[2] = '';
            $replacements[1] = '';
            $template = preg_replace($patterns, $replacements, $template);
        }
        // 1. Get from main template
        $parameters = array(
            '%app_path%' => $this->path,
            '%current_theme%' => $theme->name(),
            '%current_device%' => $this->activeTheme->device(),
            '%template%' => $template,
        );
        $templates = $this->getPaths('app_resource', $parameters);
        $file = $this->getTemplate($templates);
        if($file) {
            return $file;
        }
        // 2. get from ovveride bundle
        if ('@' === $template[0]) {
            $module = explode("/", $template);
            $patterns = array();
            $patterns[0] = '/'.$module[0].'\//';
            $patterns[1] = '/Resources\//';
            $patterns[2] = '/Templates/';
            $replacements = array();
            $replacements[2] = '';
            $replacements[1] = '';
            $replacements[0] = '';
            $templateName = preg_replace($patterns, $replacements, $template);

            if (false !== strpos($template, '..')) {
                throw new \RuntimeException(sprintf('File name "%s" contains invalid characters (..).', $template));
            }

            $bundleName = substr($template, 1);
            $path = '';
            if (false !== strpos($bundleName, '/')) {
                list($bundleName, $path) = explode('/', $bundleName, 2);
            }
            if (!preg_match('/(Bundle)$/i', $bundleName)) {
                $bundleName .= 'Bundle';
                if (0 !== strpos($path, 'Resources')) {
                    $path = 'Resources/views/'.$path;
                }
            }
            if (0 !== strpos($path, 'Resources')) {
                throw new \RuntimeException('Template files have to be in Resources.');
            }

            $parameters = array(
                '%app_path%' => $this->path,
                '%bundle_name%' => $bundleName,
                '%current_theme%' => $theme->name(),
                '%current_device%' => $this->activeTheme->device(),
                '%template%' => $templateName,
            );
            $templates = $this->getPaths('app_bundle_resource', $parameters);
            $file = $this->getTemplate($templates);
            if($file) {
                return $file;
            }
        }
        // 3. get from bundle itself

        // 4. get from default bundle
    }

    public function locateBundleTemplate($template, $dir = null, $first = true) {

        if (false !== strpos($template, '..')) {
            throw new \RuntimeException(sprintf('File name "%s" contains invalid characters (..).', $template));
        }
        $bundleName = substr($template, 1);
        $path = '';
        if (false !== strpos($bundleName, '/')) {
            list($bundleName, $path) = explode('/', $bundleName, 2);
        }
        if (!preg_match('/(Bundle)$/i', $bundleName)) {
            $bundleName .= 'Bundle';
            if (0 !== strpos($path, 'Resources')) {
                $path = 'Resources/views/'.$path;
            }
        }
        if (0 !== strpos($path, 'Resources')) {
            throw new \RuntimeException('Template files have to be in Resources.');
        }
        $resourceBundle = null;
        $bundles = $this->kernel->getBundle($bundleName, false, true);
        if (!is_array($bundles)) {
            $bundles = array($bundles);
        }
        $files = array();
        $parameters = array(
            '%bundle_name%' => $bundleName,
            '%override_path%' => substr($path, strlen('Resources/')),
            '%template%' => substr($path, strlen('Resources/views/')),
        );
        foreach ($bundles as $bundle) {
            if($bundle->getName() === $bundleName) {
                $parameters = array_merge($parameters, array(
                    '%bundle_path%' => $bundle->getPath(),
                    '%bundle_name%' => $bundle->getName(),
                ));
                $templates = $this->getPaths('bundle_resource', $parameters);
                $file = $this->getTemplate($templates);
                if($file) {
                    return $file;
                }
            }
        }
    }


    protected function getPaths($segment, $parameters)
    {
        $paths = array();
        foreach ($this->pathPatterns[$segment] as $pattern) {
            $paths[] = strtr($pattern, $parameters);
        }
        return $paths;
    }

    protected function getTemplate(array $templates = []) {
        foreach ($templates as $key => $template) {
            if (file_exists($template)) {
                return $template;
            }
        }
    }
}