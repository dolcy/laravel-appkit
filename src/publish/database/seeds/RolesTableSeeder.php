<?php

use App\Role;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class RolesTableSeeder extends Seeder {

	/**
	 * Run the Roles table seeds.
	 *
	 * @return void
	 */
	public function run()
    {
        DB::table('roles')->delete();

        /**
         * Application Administrator
         *
         **/
        Role::create([
        	'name' => 'Application Administrator',
        	'slug' => 'app.admin',
        	'description' => 'An application administrator'
        ]);

        /**
         * Application User
         *
         **/
        Role::create([
        	'name' => 'Application User',
        	'slug' => 'app.user',
        	'description' => 'A registered application user'
        ]);
    }

}
