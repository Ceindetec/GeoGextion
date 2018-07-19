<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Vehiculo extends Model
{
    //

    protected $fillable = [
        'marca_id',
        'modelo',
        'capacidad',
        'placa',
        'empresa_id',
        'estado'
    ];


    public function marca()
    {
        return $this->belongsTo(Marca::class);
    }
}
