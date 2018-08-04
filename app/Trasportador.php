<?php

namespace App;


class Trasportador extends User
{

    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class);
    }
}
