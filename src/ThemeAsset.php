<?php

namespace Olla\Theme;

use Psr\Cache\CacheItemPoolInterface;
use Olla\Theme\ActiveTheme;
class ThemeAsset
{

	protected $cacheItem;
	protected $path;
	protected $public_path;
	protected $activeTheme;
	protected $pathPatterns;

	public function __construct(
		CacheItemPoolInterface $cacheItem,
		$path = null,
		ActiveTheme $activeTheme) {
		$this->cacheItem = $cacheItem;
		$this->activeTheme = $activeTheme;
		$this->path = $path;
		$this->public_path = $path.'/public';
		$defaultPathPatterns = array(
			'react' => array(
				'%public_path%/themes/%current_theme%/devices/%current_device%/react/%filename%',
				'%public_path%/themes/%current_theme%/react/%filename%',
				'%public_path%/assets/%operation_type%/react/%filename%'
			),
			'js' => array(
				'%public_path%/themes/%current_theme%/devices/%current_device%/assets/js/%filename%',
				'%public_path%/themes/%current_theme%/assets/js/%filename%',
				'%public_path%/assets/%operation_type%/assets/js/%filename%'
			),
			'css' => array(
				'%public_path%/themes/%current_theme%/devices/%current_device%/assets/css/%filename%',
				'%public_path%/themes/%current_theme%/assets/css/%filename%',
				'%public_path%/assets/%operation_type%/assets/css/%filename%'
			),
			'fonts' => array(
				'%public_path%/themes/%current_theme%/devices/%current_device%/assets/fonts/%filename%',
				'%public_path%/themes/%current_theme%/assets/fonts/%filename%',
				'%public_path%/assets/%operation_type%/assets/fronts/%filename%'
			)
		);
		$this->pathPatterns = $defaultPathPatterns;
	}
	protected function currentTheme() {
		return $this->activeTheme->theme();
	}

	public function react(array $contexts, array $react) {
		if(null !== $theme = $this->currentTheme()) {
			$filename = isset($react['js']) ? $react['js'] : $contexts['operation'].'.js';
			$parameters = array(
				'%public_path%' => $this->public_path,
				'%operation_type%' => isset($contexts['operation_type']) ? $contexts['operation_type'] : 'default',
				'%current_theme%' => $theme->name(),
				'%current_device%' => $this->activeTheme->device(),
				'%filename%' => $filename,
			);
			$files = $this->getPaths('react', $parameters);
			$file = $this->getFile($files);
			if($file) {
				return  [
					'js' => substr($file, strlen($this->public_path.'/')),
					'component' => isset($react['component']) ? $react['component'] : 'app',
					'server' => isset($react['server']) ? true : false
				];
			}
		}
		return [];
	}

	public function js(array $contexts, array $assets) {
		$result = [];
		if(null !== $theme = $this->currentTheme()) {
			$javascripts = isset($assets['js']) ? $assets['js'] : [];

			if(isset($contexts['operation'])) {
				$javascripts = array_merge_recursive($javascripts, ['operation' => $contexts['operation'].'.js']);
				$parameters = array(
					'%public_path%' => $this->public_path,
					'%operation_type%' => isset($contexts['operation_type']) ? $contexts['operation_type'] : 'default',
					'%current_theme%' => $theme->name(),
					'%current_device%' => $this->activeTheme->device()
				);
				foreach ($javascripts as $key => $javascript) {
					$parameters = array_merge_recursive($parameters, ['%filename%' => $javascript]);
					$files = $this->getPaths('js', $parameters);
					$file = $this->getFile($files);
					if($file) {
						$result[] = substr($file, strlen($this->public_path.'/'));
					}
				}
			}
		}
		return $result;
	}
	public function css(array $contexts, array $assets) {
		$result = [];
		if(null !== $theme = $this->currentTheme()) {
			$javascripts = isset($assets['css']) ? $assets['css'] : [];
			if(isset($contexts['operation'])) {
				$javascripts = array_merge_recursive($javascripts, ['operation' => $contexts['operation'].'.css']);
				$parameters = array(
					'%public_path%' => $this->public_path,
					'%operation_type%' => isset($contexts['operation_type']) ? $contexts['operation_type'] : 'default',
					'%current_theme%' => $theme->name(),
					'%current_device%' => $this->activeTheme->device()
				);
				foreach ($javascripts as $key => $javascript) {
					$parameters = array_merge_recursive($parameters, ['%filename%' => $javascript]);
					$files = $this->getPaths('css', $parameters);
					$file = $this->getFile($files);
					if($file) {
						$result[] = substr($file, strlen($this->public_path.'/'));
					}
				}
			}
		}
		return $result;
	}

	public function fonts(array $contexts, array $assets) {
		$result = [];
		if(null !== $theme = $this->currentTheme()) {
			$javascripts = isset($assets['fonts']) ? $assets['fonts'] : [];
			if(isset($contexts['operation'])) {
				$parameters = array(
					'%public_path%' => $this->public_path,
					'%operation_type%' => isset($contexts['operation_type']) ? $contexts['operation_type'] : 'default',
					'%current_theme%' => $theme->name(),
					'%current_device%' => $this->activeTheme->device()
				);
				foreach ($javascripts as $key => $javascript) {
					$parameters = array_merge_recursive($parameters, ['%filename%' => $javascript]);
					$files = $this->getPaths('fonts', $parameters);
					$file = $this->getFile($files);
					if($file) {
						$result[] = substr($file, strlen($this->public_path.'/'));
					}
				}
			}
			
		}
		return $result;
	}

	protected function getPaths($segment, $parameters)
	{
		$paths = array();
		foreach ($this->pathPatterns[$segment] as $pattern) {
			$paths[] = strtr($pattern, $parameters);
		}
		return $paths;
	}
	protected function getFile(array $templates = []) {
		foreach ($templates as $key => $template) {
        	//echo $template;
			if (file_exists($template)) {
				return $template;
			}
		}
	}
}