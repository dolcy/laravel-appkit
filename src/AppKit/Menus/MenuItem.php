<?php namespace C5\AppKit\Menus;

/**
 * License: MIT
 * Copyright (c) 2015 Shea Lewis
 * Github: https://github.com/caffeinated
 * @package caffeinated/menus
 */
class MenuItem
{
	/**
	 * @var \C5\AppKit\Menu\MenuBuilder
	 */
	protected $builder;

	/**
	 * @var int
	 */
	public $id;

	/**
	 * @var string
	 */
	public $title;

	/**
	 * @var string
	 */
	public $slug;

	/**
	 * @var array
	 */
	public $divider = array();

	/**
	 * @var int
	 */
	public $parent;

	/**
	 * @var array
	 */
	protected $data = array();

	/**
	 * @var array
	 */
	public $attributes = array();

	/**
	 * Constructor.
	 *
	 * @param  \C5\AppKit\Menus\MenuBuilder  		$builder
	 * @param  int                        			$id
	 * @param  string                     			$title
	 * @param  array|string               			$options
	 */
	public function __construct($builder, $id, $title, $options)
	{
		$this->builder    = $builder;
		$this->id         = $id;
		$this->title      = $title;
		$this->slug       = camel_case($title);
		$this->attributes = $this->builder->extractAttributes($options);
		$this->parent     = (is_array($options) and isset($options['parent'])) ? $options['parent'] : null;

		$this->configureLink($options);
	}

	/**
	 * Configures the link for the menu item.
	 *
	 * @param  array|string  $options
	 * @return null
	 */
	public function configureLink($options)
	{
		if (! is_array($options)) {
			$path = ['url' => $options];
		} elseif (isset($options['raw']) and $options['raw'] == true) {
			$path = null;
		} else {
			$path = array_only($options, ['url', 'route', 'action', 'secure']);
		}

		if (! is_null($path)) {
			$path['prefix'] = $this->builder->getLastGroupPrefix();
		}

		$this->link = isset($path) ? new MenuLink($path) : null;

		if ($this->builder->config('auto_active') === true) {
			$this->checkActiveStatus();
		}
	}

	/**
	 * Adds a sub item to the menu.
	 *
	 * @param  string        $title
	 * @param  array|string  $options
	 * @return void
	 */
	public function add($title, $options = '')
	{
		if (! is_array($options)) {
			$url            = $options;
			$options        = array();
			$options['url'] = $url;
		}

		$options['parent'] = $this->id;

		return $this->builder->add($title, $options);
	}

	/**
	 * Add attributes to the menu item.
	 *
	 * @param  mixed
	 * @return \C5\AppKit\Menus\MenuItem|string
	 */
	public function attributes()
	{
		$args = func_get_args();

		if (isset($args[0]) and is_array($args[0])) {
			$this->attributes = array_merge($this->attributes, $args[0]);

			return $this;
		} elseif (isset($args[0]) and isset($args[1])) {
			$this->attributes[$args[0]] = $args[1];

			return $this;
		} elseif (isset($args[0])) {
			return isset($this->attributes[$args[0]]) ? $this->attributes[$args[0]] : null;
		}

		return $this->attributes;
	}

	/**
	 * Generates a valid URL for the menu item.
	 *
	 * @return string
	 */
	public function url()
	{
		if (! is_null($this->link)) {
			if ($this->link->href) {
				return $this->link->href;
			}

			return $this->builder->dispatch($this->link->path);
		}
	}

	/**
	 * Determines if the menu item has children.
	 *
	 * @return bool
	 */
	public function hasChildren()
	{
		return count($this->builder->whereParent($this->id)) or false;
	}

	/**
	 * Returns all children underneath the menu item.
	 *
	 * @return \C5\AppKit\Menu\MenuCollection
	 */
	public function children()
	{
		return $this->builder->whereParent($this->id);
	}

	/**
	 * Determines if child item url equals the provided url.
	 * !!This needs to be improved
	 *
	 * @param  string $url
	 * @return bool
	 */
	public function hasChildUrl($url)
	{
		$ret = false;

		foreach ($this->children() as $key) {
			if ($key->url() == $url ) $ret = true;
		}

		return $ret;
	}

	/**
	 * Return either a property or attribute item value.
	 *
	 * @param  string  $property
	 * @return string
	 */
	public function __get($property)
	{
		if (property_exists($this, $property)) {
			return $this->$property;
		}

		return $this->attributes[$property];
	}
}