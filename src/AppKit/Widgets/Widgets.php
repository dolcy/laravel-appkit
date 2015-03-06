<?php namespace C5\AppKit\Widgets;

use App;
use Countable;
use C5\AppKit\Exceptions\FileMissingException;
use C5\AppKit\Modules\Modules;
use Illuminate\Config\Repository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Illuminate\Contracts\View\View;

/**
 * License: MIT
 * Copyright (c) 2015 Charl Gottschalk
 * Github: https://github.com/Cloud5Ideas
 * @package cloud5ideas/appkit
 */
class Widgets implements Countable
{
	/**
	 * @var \Illuminate\Config\Repository
	 */
	protected $config;
	
	/**
	 * @var \Illuminate\Filesystem\Filesystem
	 */
	protected $files;

	/**
	 * @var \C5\AppKit\Modules\Modules
	 */
	protected $modules;

	/**
	 * @var string $path Path to the defined modules directory
	 */
	protected $path;

	/**
	 * Constructor method.
	 *
	 * @param \Illuminate\Config\Repository                $config
	 * @param \Illuminate\Filesystem\Filesystem            $files
	 */
	public function __construct(Repository $config, Filesystem $files, Modules $modules)
	{
		$this->config = $config;
		$this->files  = $files;
		$this->modules = $modules;
	}

	/**
	 * Register the widget composers for all modules.
	 *
	 * @return mixed
	 */
	public function register()
	{
		foreach ($this->modules->enabled() as $module) {
			$this->registerComposers($module);
		}
	}

	/**
	 * Register the widget composers.
	 *
	 * @param  string $module
	 * @return string
	 * @throws \C5\AppKit\Exceptions\FileMissingException
	 */
	protected function registerComposers($module)
	{
		$module    		= studly_case($module['slug']);
		$sidebarFile    = $this->modules->getPath()."/{$module}/sidebar.json";
		$dashboardFile  = $this->modules->getPath()."/{$module}/dashboard.json";

		if ($this->files->exists($sidebarFile)) {
			$content = $this->files->get($sidebarFile);
			$json[] = json_decode($content, true);
			foreach ($json[0] as $key) {
				if ($key['enabled']) {
					view()->composer($key['view'], $key['composer']);
				}
			}
		}

		if ($this->files->exists($dashboardFile)) {
			$content = $this->files->get($dashboardFile);
			$json[] = json_decode($content, true);
			foreach ($json[0] as $key) {
				if ($key['enabled']) {
					view()->composer($key['view'], $key['composer']);
				}
			}
		}
	}

		/**
	 * Get all modules.
	 *
	 * @return Collection
	 */
	public function all()
	{
		$widgets    = array();
		$allModules = $this->getAllBasenames();

		foreach ($allModules as $module) {
			$json[] = $this->getJsonContents($module);
			foreach ($json as $key) {
				foreach ($key as $value) {
					$widgets[] = $value;
				}
			}
		}

		return new Collection($widgets);
	}

	/**
	 * Get all module basenames
	 *
	 * @return array
	 */
	protected function getAllBasenames()
	{
		$modules = [];
		$path    = $this->modules->getPath();

		if ( ! is_dir($path))
			return $modules;

		$folders = $this->files->directories($path);

		foreach ($folders as $module) {
			$modules[] = basename($module);
		}
		
		return $modules;
	}

	/**
	 * Returns count of all modules.
	 *
	 * @return int
	 */
	public function count()
	{
		return count($this->all());
	}

	/**
	 * Get all widgets by enabled status.
	 *
	 * @param  bool $enabled
	 * @return array
	 */
	public function getByEnabled($enabled = true)
	{
		$disabledWidgets = array();
		$enabledWidgets  = array();
		$widgets         = $this->all();

		foreach ($widgets as $widget) {
			if ($widget['enabled']) {
				$enabledWidgets[] = $widget;
			} else {
				$disabledWidgets[] = $widget;
			}
		}

		if ($enabled === true) {
			return $enabledWidgets;
		}

		return $disabledWidgets;
	}

	/**
	 * Simple alias for getByEnabled(true).
	 *
	 * @return array
	 */
	public function enabled()
	{
		return $this->getByEnabled(true);
	}

	/**
	 * Simple alias for getByEnabled(false).
	 *
	 * @return array
	 */
	public function disabled()
	{
		return $this->getByEnabled(false);
	}


	/**
	 * Get module JSON content as an array.
	 *
	 * @param  string $module
	 * @return array|mixed
	 */
	protected function getJsonContents($module)
	{
		$module = studly_case($module);

		$default = [];

		if ( ! $this->modules->exists($module))
			return $default;

		$path = $this->getJsonPath($module);

		if ($this->files->exists($path)) {
			$contents = $this->files->get($path);
			return json_decode($contents, true);
		} else {
			return $default;
		}
	}

	/**
	 * Get path of module JSON file.
	 *
	 * @param  string $module
	 * @return string
	 */
	protected function getJsonPath($module)
	{
		return $this->modules->getModulePath($module).'/sidebar.json';
	}
}