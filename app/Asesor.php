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

    public function posiciones()
    {
        return $this->hasMany(GeoPosicion::class,'identificacion','identificacion');
    }


    public function ultimaposiciones()
    {
        return $this->hasOne(GeoPosicion::class,'identificacion','identificacion')->orderBy('created_at','desc');
    }


}
