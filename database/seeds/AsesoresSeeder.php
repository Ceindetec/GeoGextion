<?php

use App\Asesor;
use Illuminate\Database\Seeder;

class AsesoresSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //

        $asesores = Asesor::query()->insert([
            [
                'name' => 'ORFIL JOHANA',
                'email' => 'delfinorf@hotmail.com',
                'password' => bcrypt('52105131'),
                'identificacion' => '52105131',
                'nombres' => 'ORFIL JOHANA',
                'apellidos' => 'MAYOR',
                'telefono' => '3124582356',
                'estado' => 'A',
                'empresa_id' => 1
            ],
            [
                'name' => 'JOHN FABER',
                'email' => 'johnfaber890513@hotmail.com',
                'password' => bcrypt('1030556368'),
                'identificacion' => '1030556368',
                'nombres' => 'JOHN FABER',
                'apellidos' => 'RODRIGUEZ',
                'telefono' => '3115648945',
                'estado' => 'A',
                'empresa_id' => 1
            ],
            [
                'name' => 'MARIA NUBIA',
                'email' => 'ng3184244@gmail.com',
                'password' => bcrypt('52473670'),
                'identificacion' => '52473670',
                'nombres' => 'MARIA NUBIA',
                'apellidos' => 'CHACON',
                'telefono' => '3205641245',
                'estado' => 'A',
                'empresa_id' => 1
            ],
            [
                'name' => 'FREDY',
                'email' => 'fredychamo@yahoo.com',
                'password' => bcrypt('79621679'),
                'identificacion' => '79621679',
                'nombres' => 'FREDY',
                'apellidos' => 'CHACON',
                'telefono' => '3102563458',
                'estado' => 'A',
                'empresa_id' => 2
            ],
            [
                'name' => 'NICOLAS',
                'email' => 'elazrael777@gmail.com',
                'password' => bcrypt('1019071749'),
                'identificacion' => '1019071749',
                'nombres' => 'NICOLAS',
                'apellidos' => 'DIAZ CHACON',
                'telefono' => '3156984521',
                'estado' => 'A',
                'empresa_id' => 2
            ],
            [
                'name' => 'GERALDINE',
                'email' => 'saraeisa1607@outlook.com',
                'password' => bcrypt('1026571416'),
                'identificacion' => '1026571416',
                'nombres' => 'GERALDINE',
                'apellidos' => 'AGUILAR',
                'telefono' => '3216587432',
                'estado' => 'A',
                'empresa_id' => 2
            ],
            [
                'name' => 'PAOLA',
                'email' => 'lislau16@hotmail.com',
                'password' => bcrypt('1030578176'),
                'identificacion' => '1030578176',
                'nombres' => 'PAOLA',
                'apellidos' => 'MUÑOZ SANCHEZ',
                'telefono' => '3215698742',
                'estado' => 'A',
                'empresa_id' => 3
            ],
            [
                'name' => 'PAOLA ANDREA',
                'email' => 'andrea.2283@hotmail.com',
                'password' => bcrypt('52987428'),
                'identificacion' => '52987428',
                'nombres' => 'PAOLA ANDREA',
                'apellidos' => 'PEDRAZA',
                'telefono' => '3204932172',
                'estado' => 'A',
                'empresa_id' => 3
            ],
            [
                'name' => 'MIGUEL ANGEL',
                'email' => 'miguelaltamars@gmail.com',
                'password' => bcrypt('72289819'),
                'identificacion' => '72289819',
                'nombres' => 'MIGUEL ANGEL',
                'apellidos' => 'ALTAMAR',
                'telefono' => '3103254194',
                'estado' => 'A',
                'empresa_id' => 3
            ],
            [
                'name' => 'ARLEX',
                'email' => 'arlex.contam@gmail.com',
                'password' => bcrypt('101024617'),
                'identificacion' => '101024617',
                'nombres' => 'ARLEX',
                'apellidos' => 'CONTRERAS',
                'telefono' => '3221035847',
                'estado' => 'A',
                'empresa_id' => 4
            ],
            [
                'name' => 'MARY ALEJANDRA',
                'email' => 'alejandraalfonso.2011@gmail.com',
                'password' => bcrypt('52240980'),
                'identificacion' => '52240980',
                'nombres' => 'MARY ALEJANDRA',
                'apellidos' => 'ALFONSO',
                'telefono' => '3152439865',
                'estado' => 'A',
                'empresa_id' => 4
            ],
            [
                'name' => 'CARLOS STEVEN',
                'email' => 'carlostorresvallejo@hotmail.com',
                'password' => bcrypt('1016037061'),
                'identificacion' => '1016037061',
                'nombres' => 'CARLOS STEVEN',
                'apellidos' => 'TORRES',
                'telefono' => '3162048723',
                'estado' => 'A',
                'empresa_id' => 4
            ],
            [
                'name' => 'DANIEL',
                'email' => 'damemo16@hotmail.com',
                'password' => bcrypt('16270094'),
                'identificacion' => '16270094',
                'nombres' => 'DANIEL',
                'apellidos' => 'MEDINA',
                'telefono' => '3146523168',
                'estado' => 'A',
                'empresa_id' => 5
            ],
            [
                'name' => 'DEISSY',
                'email' => 'coindepigo@hotmail.com',
                'password' => bcrypt('52878399'),
                'identificacion' => '52878399',
                'nombres' => 'DEISSY',
                'apellidos' => 'PINEDA',
                'telefono' => '3125687421',
                'estado' => 'A',
                'empresa_id' => 5
            ],

            [
                'name' => 'DIANA',
                'email' => 'ldj2909@hotmail.com',
                'password' => bcrypt('53006165'),
                'identificacion' => '53006165',
                'nombres' => 'DIANA',
                'apellidos' => 'MARITZA AGUILAR',
                'telefono' => '3126589432',
                'estado' => 'A',
                'empresa_id' => 5
            ],

        ]);
    }
}
