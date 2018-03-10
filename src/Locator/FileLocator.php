<?php


namespace Olla\Theme\Locator;

use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Config\FileLocator as BaseFileLocator;
use Olla\Theme\Theme;

class FileLocator extends BaseFileLocator
{
    protected $kernel;
    protected $path;
    protected $basePaths = array();
    protected $pathPatterns;
    protected $theme;



    public function __construct(
        KernelInterface $kernel,
        $path = null,
        array $paths = array(),
        array $pathPatterns = array(),
        Theme $theme
    ) {
        $this->kernel = $kernel;
        $this->path = $path;
        $this->basePaths = $paths;
        $this->theme = $theme;

        $defaultPathPatterns = array(
            'app_resource' => array(
                '%app_path%/themes/%current_theme%/%template%',
                '%app_path%/views/%template%',
            ),
            'bundle_resource' => array(
                '%bundle_path%/Resources/themes/%current_theme%/%template%',
            ),
            'bundle_resource_dir' => array(
                '%dir%/themes/%current_theme%/%bundle_name%/%template%',
                '%dir%/%bundle_name%/%override_path%',
            ),
        );
        $this->pathPatterns = array_merge_recursive(array_filter($pathPatterns), $defaultPathPatterns);
        parent::__construct(array());
    }

    /**
     * Set the active theme.
     *
     * @param string $theme
     * @param string $deviceType
     */
    public function setCurrentTheme($theme, string $deviceType = null)
    {
        $paths = $this->basePaths;
        $paths[] = $this->path.'/themes/'.$theme;
        $paths[] = $this->path;
        $this->paths = $paths;
    }


    public function locate($name, $dir = null, $first = true)
    {

        $themeName = $this->theme->getTheme();
        $this->setCurrentTheme($themeName);
        if ('@' === $name[0]) {
            $view = $this->locateBundleResource($name, $this->path, $first);
            if($view) {
                return $view;
            } else {
                $module = explode("/", $name);
                $patterns = array();
                $patterns[0] = '/'.$module[0].'\//';
                $patterns[1] = '/::/';
                $patterns[2] = '//';
                $replacements = array();
                $replacements[2] = '';
                $replacements[1] = '';
                $replacements[0] = '';
                $name = preg_replace($patterns, $replacements, $name);
            }
        }
    

        if (0 === strpos($name, 'views/')) {
            if ($res = $this->locateAppResource($name, $this->path, $first)) {
                return $res;
            }
        }
        return parent::locate($name, $dir, $first);
    }

    protected function locateBundleResource($name, $dir = null, $first = true)
    {

        if (false !== strpos($name, '..')) {
            throw new \RuntimeException(sprintf('File name "%s" contains invalid characters (..).', $name));
        }

        $bundleName = substr($name, 1);

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

        // Symfony 4+ no longer supports inheritance and so we only get a single bundle
        if (!is_array($bundles)) {
            $bundles = array($bundles);
        }
        $files = array();

        $parameters = array(
            '%app_path%' => $this->path,
            '%dir%' => $dir,
            '%override_path%' => substr($path, strlen('Resources/')),
            '%current_theme%' => $this->theme->getTheme(),
            '%current_device%' => $this->theme->getDevice(),
            '%template%' => substr($path, strlen('Resources/views/')),
        );

        foreach ($bundles as $bundle) {
            $parameters = array_merge($parameters, array(
                '%bundle_path%' => $bundle->getPath(),
                '%bundle_name%' => $bundle->getName(),
            ));

            $checkPaths = $this->getPathsForBundleResource($parameters);

            foreach ($checkPaths as $checkPath) {
                if (file_exists($checkPath)) {
                    if (null !== $resourceBundle) {
                        throw new \RuntimeException(sprintf('"%s" resource is hidden by a resource from the "%s" derived bundle. Create a "%s" file to override the bundle resource.',
                            $path,
                            $resourceBundle,
                            $checkPath
                        ));
                    }

                    if ($first) {
                        return $checkPath;
                    }
                    $files[] = $checkPath;
                }
            }

            $file = $bundle->getPath().'/'.$path;

            if (file_exists($file)) {
                if ($first) {
                    return $file;
                }
                $files[] = $file;
                $resourceBundle = $bundle->getName();
            }
        }

        if (count($files) > 0) {
            return $first ? $files[0] : $files;
        }
    }

    protected function locateAppResource($name, $dir = null, $first = true)
    {

        if (false !== strpos($name, '..')) {
            throw new \RuntimeException(sprintf('File name "%s" contains invalid characters (..).', $name));
        }

        $files = array();
        $parameters = array(
            '%app_path%' => $this->path,
            '%current_theme%' => $this->theme->getTheme(),
            '%current_device%' => $this->theme->getDevice(),
            '%template%' => substr($name, strlen('/views')),
        );

        foreach ($this->getPathsForAppResource($parameters) as $checkPaths) {

            if (file_exists($checkPaths)) {
                if ($first) {
                    return $checkPaths;
                }
                $files[] = $checkPaths;
            }
        }

        return $files;
    }

    protected function getPathsForBundleResource($parameters)
    {
        $pathPatterns = array();
        $paths = array();

        if (!empty($parameters['%dir%'])) {
            $pathPatterns = array_merge($pathPatterns, $this->pathPatterns['bundle_resource_dir']);
        }

        $pathPatterns = array_merge($pathPatterns, $this->pathPatterns['bundle_resource']);
        foreach ($pathPatterns as $pattern) {
            $paths[] = strtr($pattern, $parameters);
        }
        return $paths;
    }

    protected function getPathsForAppResource($parameters)
    {
        $paths = array();

        foreach ($this->pathPatterns['app_resource'] as $pattern) {
            $paths[] = strtr($pattern, $parameters);
        }
        return $paths;
    }
}