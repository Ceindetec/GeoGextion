<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GeoPosicion extends Model
{
    protected $fillable = ['latitud','longitud','identificacion'];

    public function getAsesor(){
        return $this->belongsTo(Asesor::class,'identificacion','identificacion');
    }
}
