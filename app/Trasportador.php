<?php

namespace App;


class Trasportador extends User
{

    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class);
    }

    public function supertransportador()
    {
        return $this->belongsToMany(Asesor::class,'supertransporte_transportador','transportador_id','supertransporte_id');
    }

    public function ultimaposiciones()
    {
        return $this->hasOne(GeoPosicion::class,'identificacion','identificacion')->orderBy('created_at','desc');
    }
}
