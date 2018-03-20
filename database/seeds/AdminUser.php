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
        $userId = DB::table('users')->insertGetId([
            'name' => 'admin',
            'email' => 'geoadmin@gmail.com',
            'password' => bcrypt('123456789'),
        ]);

        $roleId = DB::table('roles')->insertGetId(
            [
                "name" => "SuperAdmin",
                "slug" => "superadmin",
                "Description" => "Control total sistema"
            ]);
        DB::table('roles')->insert([[
            "name" => "SuperAdministradorEmpresa",
            "slug" => "sadminempresa",
            "Description" => "Control total de la empresa"
        ],
            [
                "name" => "Administrador",
                "slug" => "admin",
                "Description" => "Control sobre los supervisores y asesores del sistema"
            ],
            [
                "name" => "Supervisor",
                "slug" => "super",
                "Description" => "Superviza a los asesores"
            ]]);

        DB::table('role_user')->insert([
            "role_id" => $roleId,
            "user_id" => $userId
        ]);
    }
}
