<?php

namespace App;


class SupervisorTransporte extends User
{
    //

    public function transportadores()
    {
        return $this->belongsToMany(Asesor::class,'supertransporte_transportador','supertransporte_id','transportador_id');
    }
}
