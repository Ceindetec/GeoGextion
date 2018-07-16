<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Empresas extends Model
{
    protected $fillable = ['nit','razon','abreviatura','telefono','direccion','estado'];



    public function User(){
        return $this->hasMany(User::class,'empresa_id','id');
    }

}
