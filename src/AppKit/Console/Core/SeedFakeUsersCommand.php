<?php namespace C5\AppKit\Console\Core;

use Illuminate\Console\Command;

/**
 * License: MIT
 * Copyright (c) 2015 Charl Gottschalk
 * Github: https://github.com/Cloud5Ideas
 * @package cloud5ideas/appkit
 */
class SeedFakeUsersCommand extends Command
{
	/**
	 * @var string $name The console command name.
	 */
	protected $name = 'appkit:fake:users';

	/**
	 * @var string $description The console command description.
	 */
	protected $description = 'Seed some fake users.';

	/**
	 * Create a new command instance.
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		$this->call('db:seed', ['--class'=> 'FakeUsersSeeder']);
	}
}
