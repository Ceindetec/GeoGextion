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

            $config = array();
            $config['center'] = 'auto';
            $config['onboundschanged'] = 'if (!centreGot) {
            var mapCentre = map.getCenter();
            marker_0.setOptions({
                position: new google.maps.LatLng(mapCentre.lat(), mapCentre.lng())
                });
            }
            centreGot = true;';

            app('map')->initialize($config);
            $direccion = app('map')->get_address_from_lat_long($request->latitud, $request->longitud);

            $geposicion->direccion = $direccion[0];

            $geposicion->fecha = Carbon::now();
            $geposicion->save();
            $result['estado'] = true;
            $result['mensaje'] = 'registrado';
        } catch (\Exception $exception) {
            $result['estado'] = false;
            $result['mensaje'] = 'Error durante la insercion';
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
