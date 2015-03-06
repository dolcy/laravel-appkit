<?php

use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Faker\Factory;

class FakeUsersSeeder extends Seeder {

	/**
	 * Run the Roles table seeds.
	 *
	 * @return void
	 */
	public function run()
    {
        DB::table('users')->where('id', '>', 1)->delete();
        $faker = Factory::create();

        for ($i=1; $i < 20; $i++) { 
            User::create([
                'first_name' => $faker->firstName,
                'last_name' => $faker->lastName,
                'email' => $faker->email,
                'password' => bcrypt($faker->password),
                'is_superuser' => $faker->boolean(),
                'is_disabled' => $faker->boolean(),
                'image' => $faker->imageUrl(300, 300),
                'key' => str_random(21)
            ]);
        }
    }

}
