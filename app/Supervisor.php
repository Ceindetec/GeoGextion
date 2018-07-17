<?php

namespace App;


use Caffeinated\Shinobi\Models\Role;

class Supervisor extends User
{
    //
    //
    //protected $table = 'users';

    public function asesores()
    {
        return $this->belongsToMany(Asesor::class,'asesor_supervisor','supervisor_id','asesor_id');
    }


}
