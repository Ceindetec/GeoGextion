<?php

namespace App;

use Caffeinated\Shinobi\Models\Role;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Caffeinated\Shinobi\Traits\ShinobiTrait;

class User extends Authenticatable
{
    use Notifiable, ShinobiTrait;


    const SUPERADMIN = 1;
    const SUPERADMINEMPRESA = 2;
    const ADMINISTRADOR = 3;
    const SUPERVISORA = 4;
    const ASESOR = 5;
    const SUPERVISRORT = 6;
    const TRASPORTADOR = 7;


    protected $table= 'users';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','identificacion','nombres','apellidos','email','telefono','vehiculo_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    /*protected $hidden = [
        'password', 'remember_token',
    ];*/


    public function empresa()
    {
        return $this->belongsTo(Empresas::class);
    }


    public function rol(){
        return $this->belongsToMany(Role::class,'role_user','user_id','role_id');
    }


}
