<?php namespace C5\AppKit\Console\Core;

use C5\AppKit\Handlers\Core\InstallHandler;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;

/**
 * License: MIT
 * Copyright (c) 2015 Charl Gottschalk
 * Github: https://github.com/Cloud5Ideas
 * @package cloud5ideas/appkit
 */
class InstallCommand extends Command
{
	/**
	 * @var string $name The console command name.
	 */
	protected $name = 'appkit:install';

	/**
	 * @var string $description The console command description.
	 */
	protected $description = 'Install AppKit';

	/**
	 * @var \C5\AppKit\Handlers\Core\InstallHandler
	 */
	protected $handler;

	/**
	 * Create a new command instance.
	 *
	 * @param \C5\AppKit\Handlers\Core\InstallHandler $handler
	 */
	public function __construct(InstallHandler $handler)
	{
		parent::__construct();
		$this->handler = $handler;
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		return $this->handler->fire($this, $this->option('optimize'));
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return [
			['optimize', null, InputOption::VALUE_NONE, 'Optimize Laravel and AppKit.']
		];
	}
}
