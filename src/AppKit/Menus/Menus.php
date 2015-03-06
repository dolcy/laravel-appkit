<?php namespace C5\AppKit\Menus;

use View;
use Illuminate\Html\HtmlBuilder;
use Illuminate\Config\Repository;
use Illuminate\Routing\UrlGenerator;

/**
 * License: MIT
 * Copyright (c) 2015 Shea Lewis
 * Github: https://github.com/caffeinated
 * @package caffeinated/menus
 */
class Menus
{
	/**
	 * @var \C5\AppKit\Menus\MenuCollection
	 */
	protected $collection;

	/**
	 * @var \Illuminate\Config\Repository
	 */
	protected $config;

	/**
	 * @var \Illuminate\Html\HtmlBuilder
	 */
	protected $html;

	/**
	 * @var \Illuminate\Routing\UrlGenerator
	 */
	protected $url;

	/**
	 * @var \Illuminate\View\Factory
	 */
	protected $view;

	/**
	 * Constructor.
	 *
	 * @param  \Illuminate\Config\Repository     $config
	 * @param  \Illuminate\View\Factory          $view
	 * @param  \Illuminate\Html\HtmlBuilder      $html
	 * @param  \Illuminate\Routing\UrlGenerator  $url
	 */
	public function __construct(Repository $config, HtmlBuilder $html, UrlGenerator $url)
	{
		$this->config     = $config;
		$this->html       = $html;
		$this->url        = $url;
		$this->collection = new MenuCollection;
	}

	/**
	 * Create a new menu instance.
	 *
	 * @param  string    $name
	 * @param  callable  $callback
	 * @return \C5\AppKit\Menus\MenuBuilder
	 */
	public function make($name, $callback, $share = null)
	{
		if (is_callable($callback)) {
			$menu = new MenuBuilder($name, $this->loadConfig($name), $this->html, $this->url);

			call_user_func($callback, $menu);

			$this->collection->put($name, $menu);

			if ( $share != null ) {
				View::composer($share, function($view) use ($name, $menu)
		        {
		        	$view->with($name, $menu);
		        });
			}

			return $menu;
		}
	}

	/**
	 * Loads and merges configuration data.
	 *
	 * @param  string  $name
	 * @return array
	 */
	public function loadConfig($name)
	{
		$options = $this->config->get('menu.settings');
		$name    = strtolower($name);

		if (isset($options[$name]) and is_array($options[$name])) {
			return array_merge($options['default'], $options[$name]);
		}

		return $options['default'];
	}

	/**
	 * Find and return the given menu collection.
	 *
	 * @param  string  $key
	 * @return \C5\AppKit\Menus\MenuCollection
	 */
	public function get($key)
	{
		return $this->collection->get($key);
	}

	/**
	 * Returns all menu instances.
	 *
	 * @return \C5\AppKit\Menus\MenuCollection
	 */
	public function all()
	{
		return $this->collection;
	}
}