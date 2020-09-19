<?php

use App\User;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
            User::create([
               'name' => 'rizky',
               'username' => 'rizky',
               'password' => bcrypt('password'),
               'email' => 'rizky@gmail.com',

            ]);

    }
}
