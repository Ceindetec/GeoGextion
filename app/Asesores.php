<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Asesores extends User
{
    //
    protected $fillable = ['identificacion','nombres','apellidos','email','telefono','empresa_id'];

    public function getPosition(){
        return $this->hasMany('App\GeoPosicion','identificacion','identificacion')
            ->whereDate('fecha',Carbon::now()->format('Y-m-d'))
            ->orderBy('fecha','asc');
    }

    public function getRuta($fecha){
        return $this->hasMany('App\GeoPosicion','identificacion','identificacion')
            ->whereDate('fecha',$fecha)
            ->orderBy('fecha','asc');
    }

    public function supervisor()
    {
        $this->belongsToMany(Supervisor::class);
    }
}
