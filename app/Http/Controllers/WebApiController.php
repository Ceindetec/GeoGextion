<?php

namespace App\Http\Controllers;

use App\Asesores;
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
            $result['mensaje'] = 'Error durante la insercion';
        }
        return $result;
    }

    public function validaIdentificacion(Request $request){
        $result = [];
        try{
            $asesor = Asesores::where('identificacion',$request->identificacion)->first();
            if($asesor != null){
                $result['estado'] = true;
                $result['mensaje'] = 'Identificacion validad';
            }else{
                $result['estado'] = false;
                $result['mensaje'] = 'Verifique la identificacion';
            }
        }catch (\Exception $exception){
            $result['estado'] = false;
            $result['mensaje'] = 'Ocurrio un error '.$exception;
        }
        return $result;
    }
}
