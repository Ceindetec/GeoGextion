<?php

use Illuminate\Database\Seeder;

class AdminUser extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'PRUEBA',
            'email' => 'prueba@gmail.com',
            'password' => bcrypt('123'),
        ]);
    }
}
