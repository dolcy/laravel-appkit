<?php namespace C5\AppKit\Handlers\Core;

use App\User;
use Illuminate\Console\Command;

/**
 * License: MIT
 * Copyright (c) 2015 Charl Gottschalk
 * Github: https://github.com/Cloud5Ideas
 * @package cloud5ideas/appkit
 */
class InstallHandler
{
	/**
	 * @var Console
	 */
	protected $console;


	/**
	 * Constructor method.
	 *
	 */
	public function __construct()
	{

	}

	/**
	 * Fire off the handler.
	 *
	 * @param  \C5\AppKit\Console\Core\InstallCommand $console
	 * @param  string $name
	 * @return bool
	 */
	public function fire(Command $console, $optimize = false)
	{
		$this->console = $console;

		$this->console->info('AppKit will now run all migrations and seeders.');

		if ($this->console->confirm('Would you like to continue? [yes|no]'))
		{
			$this->console->call('appkit:migrate');
			$this->console->call('appkit:seed');

			$this->createUser();
			$this->optimize($optimize);
			$this->serve($optimize);
		}
	}

	/**
	 * Optimize Laravel.
	 *
	 * @return void
	 */
	protected function optimize($optimize = false) {
		if ($optimize) {
		    $this->console->call('optimize');
		} else {
			if ($this->console->confirm('Would you like to optimize Laravel? [yes|no]'))
			{
				$this->console->call('optimize');
			}
		}
	}

	/**
	 * Serve Application.
	 *
	 * @return void
	 */
	protected function serve() {
		if ($this->console->confirm('Would you like to serve your application? [yes|no]'))
		{
			$this->console->call('serve');
		}
	}

	/**
	 * Create the admin superuser.
	 *
	 * @return void
	 */
	protected function createUser()
	{
		try {
			$this->console->info('=====LETS CREATE AN ADMIN SUPERUSER=====');
			$firstName = $this->console->ask('What is the admin\'s first name?');
			$lastName = $this->console->ask('What is the admin\'s last name?');
			$email = $this->console->ask('What is the admin\'s email address?');
			$password = $this->console->secret('Choose a password, and make it strong.');
			User::create([
				'first_name' => $firstName,
				'last_name' => $lastName,
				'email' => $email,
				'password' => bcrypt($password),
				'is_superuser' => true,
				'key' => str_random(21)
			]);
			$this->console->info('=====ADMIN SUPERUSER CREATED=====');
			$this->console->info('Nice! You can now login to your application with the superuser email and password.');
		} catch (Exception $e) {
			$this->console->error('Whoops! Something went wrong trying to create your user.');
			$this->console->error('=====EXCEPTION START=====');
			$this->console->error($e);
			$this->console->error('=====EXCEPTION END=====');
		}
	}
}
