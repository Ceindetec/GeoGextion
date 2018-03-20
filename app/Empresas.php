<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Empresas extends Model
{
    protected $fillable = ['nit','razon','abreviatura','telefono','direccion','estado'];



}
