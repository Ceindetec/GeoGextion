<?php

use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
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
                'id' => 1,
                "name" => "SuperAdmin",
                "slug" => "superadmin",
                "Description" => "Control total sistema"
            ]);
        DB::table('roles')->insert(
            [
                'id' => 2,
                "name" => "SuperAdministradorEmpresa",
                "slug" => "sadminempresa",
                "Description" => "Control total de la empresa"
            ],
            [
                'id' => 3,
                "name" => "Administrador",
                "slug" => "admin",
                "Description" => "Control sobre los supervisores y asesores del sistema"
            ],
            [
                'id' => 4,
                "name" => "Supervisor",
                "slug" => "super",
                "Description" => "Superviza a los asesores"
            ],
            [
                'id' => 5,
                "name" => "Asesor",
                "slug" => "asesor",
                "Description" => "asesores de visitas"
            ],
            [
                'id' => 6,
                "name" => "Supervisor Trasporte",
                "slug" => "supert",
                "Description" => "Superviza a los trasportadores"
            ],
            [
                'id' => 7,
                "name" => "Trasportador",
                "slug" => "trasporte",
                "Description" => "Se encarga de los trasportes"
            ]
        );

        DB::table('role_user')->insert([
            "role_id" => $roleId,
            "user_id" => $userId
        ]);
    }
}
