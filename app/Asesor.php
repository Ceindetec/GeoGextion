<?php

namespace App;


use Caffeinated\Shinobi\Models\Role;

class Asesor extends User
{
    //



    //protected $table = 'users';

    public function supervisor()
    {
        return $this->belongsToMany(Supervisor::class,'asesor_supervisor','asesor_id','supervisor_id');
    }




}
