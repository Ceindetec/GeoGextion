<?php

namespace App\Http\Controllers;

use App\GeoPosicion;
use Carbon\Carbon;
use Illuminate\Http\Request;

class WebApiController extends Controller
{
    //

    public function nuevoPunto(Request $request){
        $result = [];
        try{
            $geposicion = new GeoPosicion($request->all());
            $geposicion->fecha = Carbon::now();
            $geposicion->save();
            $result['estado'] = true;
            $result['mensaje'] = 'registrado';
        }catch (\Exception $exception){
            $result['estado'] = false;
            $result['mensaje'] = 'Error '.$exception->getMessage();
        }
        return $result;
    }
}
