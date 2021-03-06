<?php

use Illuminate\Database\Seeder;
use App\Seeds\RolesTableSeeder;
use App\Seeds\UsersTableSeeder;
use App\Seeds\UsersRolesTableSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         $this->call(RolesTableSeeder::class);
         $this->call(UsersTableSeeder::class);
         $this->call(UsersRolesTableSeeder::class);
    }
}
