<?php namespace C5\AppKit\Handlers\Modules;

use C5\AppKit\Modules\Modules;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

/**
 * License: MIT
 * Copyright (c) 2015 Charl Gottschalk
 * Github: https://github.com/Cloud5Ideas
 * @package cloud5ideas/appkit
 */
class MakeModuleWidgetHandler
{
	/**
	 * @var Console
	 */
	protected $console;

	/**
	 * @var array $folders Module folders to be created.
	 */
	protected $folders = [
		'Composers/',
		'Composers/{{widget}}/',
		'Resources/Views/{{widget}}/'
	];

	/**
	 * @var \C5\AppKit\Modules\Modules
	 */
	protected $module;

	/**
	 * @var \Illuminate\Filesystem\Filesystem
	 */
	protected $finder;

	/**
	 * @var string
	 */
	protected $slug;

	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var string
	 */
	protected $widget;

	/**
	 * @var string
	 */
	protected $container;

	/**
	 * @var string
	 */
	protected $lower;

	/**
	 * Constructor method.
	 *
	 * @param \C5\AppKit\Modules\Modules      $module
	 * @param \Illuminate\Filesystem\Filesystem $finder
	 */
	public function __construct(Modules $module, Filesystem $finder)
	{
		$this->module = $module;
		$this->finder  = $finder;
	}

	/**
	 * Fire off the handler.
	 *
	 * @param  \C5\AppKit\Console\ModuleMake*WidgetCommand $console
	 * @param  string                                         $slug
	 * @return bool
	 */
	public function fire(Command $console, $slug, $name, $widget)
	{
		$this->console 		= $console;
		$this->slug    		= $slug;
		$this->name    		= studly_case($name);
		$this->lower    	= strtolower($name);
		$this->container	= '\\'.studly_case($widget);
		$this->widget 		= studly_case($widget);

		$this->generate($console);
	}

	/**
	 * Generate widget folders and files.
	 *
	 * @param  \C5\AppKit\Console\ModuleMake*WidgetCommand $console
	 * @return boolean
	 */
	public function generate(Command $console)
	{
		$this->generateFolders();
		$this->generateFiles();

		$console->info("Module {$this->widget} widget [{$this->name}] has been created successfully.");

		return true;
	}

	/**
	 * Generate defined widget folders.
	 *
	 * @return void
	 */
	protected function generateFolders()
	{
		foreach ($this->folders as $folder) {
			$dir = $this->getModulePath($this->slug).$this->formatContent($folder);
			if (! $this->finder->isDirectory($dir)) {
				$this->finder->makeDirectory($dir);
			}
		}
	}

	/**
	 * Generate defined widget files.
	 *
	 * @return void
	 */
	protected function generateFiles()
	{
		$composerFile = $this->formatContent('Composers/{{widget}}/{{name}}{{widget}}WidgetComposer.php');
		$this->makeFile('WidgetComposer', $composerFile);
		$viewFile = $this->formatContent('Resources/Views/{{widget}}/{{lower}}.blade.php');
		$this->makeFile($this->widget.'Widget', $viewFile);
		$configFile = strtolower($this->widget).'.json';
		$configFileDest = $this->getDestinationFile($configFile);
		if (! $this->finder->exists($configFileDest) ) {
			$this->makeFile($this->widget.'Config', $configFile);
		} else {
			$content = $this->finder->get($configFileDest);
			$json[] = json_decode($content, true);
			$widgetConfig = [
				'requiresAccess' => true, 
				'requiredAccess' => $this->slug.'.permission', 
				'view' => $this->slug.'::'.$this->widget.'.'.$this->lower, 
				'composer' => $this->module->getNamespace().studly_case($this->slug).'\\Composers\\'.$this->widget.'\\'.$this->name.$this->widget.'WidgetComposer', 
				'enabled' => true
			];
			array_push($json[0], $widgetConfig);
			$this->finder->put($configFileDest, json_encode($json[0], JSON_PRETTY_PRINT));
		}
	}

	/**
	 * Create widget file.
	 *
	 * @param  int     $key
	 * @param  string  $file
	 * @return int
	 */
	protected function makeFile($key, $file)
	{
		return $this->finder->put($this->getDestinationFile($file), $this->getStubContent($key));
	}

	/**
	 * Get the path to the module.
	 *
	 * @param  string $slug
	 * @return string
	 */
	protected function getModulePath($slug = null, $allowNotExists = false)
	{
		if ($slug)
			return $this->module->getModulePath($slug, $allowNotExists);

		return $this->module->getPath();
	}

	/**
	 * Get destination file.
	 *
	 * @param  string $file
	 * @return string
	 */
	protected function getDestinationFile($file)
	{
		return $this->getModulePath($this->slug).$this->formatContent($file);
	}

	/**
	 * Get stub content by key.
	 *
	 * @param  int $key
	 * @return string
	 */
	protected function getStubContent($key)
	{
		return $this->formatContent($this->finder->get(__DIR__.'/../../Stubs/Modules/Module'.$key.'.stub'));
	}

	/**
	 * Replace placeholder text with correct values.
	 *
	 * @return string
	 */
	protected function formatContent($content)
	{
		return str_replace(
			['{{name}}', '{{slug}}', '{{module}}', '{{namespace}}', '{{escaped_namespace}}', '{{lower}}', '{{widget}}', '{{container}}'],
			[$this->name, $this->slug, studly_case($this->slug), $this->module->getNamespace(), addslashes($this->module->getNamespace()), $this->lower, $this->widget, $this->container],
			$content
		);
	}
}
