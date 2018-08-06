<?php

namespace App\Http\Controllers;

use App\Asesores;
use App\GeoPosicion;
use Carbon\Carbon;
use Illuminate\Http\Request;

class WebApiController extends Controller
{
    //

    public function nuevoPunto(Request $request)
    {
        $result = [];
        try {
            $geposicion = new GeoPosicion($request->all());


            $data_location = "https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=" . $request->latitud . "&lon=" . $request->longitud;

            $opts = array('http' => array('header' => "User-Agent: StevesCleverAddressScript 3.7.6\r\n"));
            $context = stream_context_create($opts);

            // Open the file using the HTTP headers set above
            $data = file_get_contents($data_location, false, $context);


            $data = json_decode($data);

            $geposicion->direccion = $data->display_name;

            $geposicion->fecha = Carbon::now();
            $geposicion->save();
            $result['estado'] = true;
            $result['mensaje'] = 'registrado';
        } catch (\Exception $exception) {
            $result['estado'] = false;
            $result['mensaje'] = 'Error durante la insercion ' . $exception->getMessage();
        }
        return $result;
    }

    public function validaIdentificacion(Request $request)
    {
        $result = [];
        try {
            $asesor = Asesores::where('identificacion', $request->identificacion)->first();
            if ($asesor != null) {
                $result['estado'] = true;
                $result['mensaje'] = 'Identificacion validad';
            } else {
                $result['estado'] = false;
                $result['mensaje'] = 'Verifique la identificacion';
            }
        } catch (\Exception $exception) {
            $result['estado'] = false;
            $result['mensaje'] = 'Ocurrio un error ' . $exception;
        }
        return $result;
    }
}
