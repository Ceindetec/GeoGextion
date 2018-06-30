<?php

namespace App\Http\Controllers;

use App\Asesores;
use Caffeinated\Shinobi\Facades\Shinobi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class AsesorController extends Controller
{
    //

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function listaAsesores()
    {
        return view('asesores.listaasesores');
    }

    public function gridAsesores()
    {
        if (Shinobi::isRole('admin')||Shinobi::isRole('sadminempresa')) {
            $asesores = Asesores::where('asesores.empresa_id', Auth::user()->empresa_id);
        } else {
            $asesores = Asesores::join('user_asesors', 'asesores.id', 'user_asesors.asesore_id')
                ->select('asesores.*')
                ->where('asesores.estado', 'A')
                ->where('user_asesors.user_id', Auth::User()->id)
                ->where('asesores.empresa_id', Auth::user()->empresa_id)
                ->get();
        }
        return DataTables::of($asesores)
            ->addColumn('action', function ($asesores) {
                $acciones = '<div class="btn-group">';
                $acciones .= '<a class="btn btn-xs btn-success" data-modal href="' . route('asesor.editar', $asesores->id) . '">Editar</a>';
                if ($asesores->estado == 'A')
                    $acciones .= '<button class="btn btn-xs btn-danger" onclick="cambiarestado(' . $asesores->id . ')">Inactivar</button>';
                else
                    $acciones .= '<button class="btn btn-xs btn-success" onclick="cambiarestado(' . $asesores->id . ')">Activar</button>';
                $acciones .= '</div>';
                return $acciones;
            })
            ->make(true);
    }

    public function viewCrearAsesor()
    {
        return view('asesores.modalcrearasesor');
    }

    public function crearAsesor(Request $request)
    {
        $result = [];
        try {
            $validator = \Validator::make($request->all(), [
                'identificacion' => 'required|unique:asesores|max:11',
                'email' => 'unique:asesores',
            ]);

            if ($validator->fails()) {
                return $validator->errors()->all();
            }

            $asesor = new Asesores($request->all());
            $asesor->empresa_id = Auth::user()->empresa_id;
            $asesor->save();
            $result['estado'] = true;
            $result['mensaje'] = 'Asesor agregado satisfactoriamente.';
        } catch (\Exception $exception) {
            $result['estado'] = false;
            $result['mensaje'] = 'Asesor agregado satisfactoriamente. ' . $exception->getMessage();
        }
        return $result;

    }

    public function viewEditarAsesor($id)
    {
        $asesor = Asesores::find($id);
        return view('asesores.modaleditarasesor', compact('asesor'));
    }

    public function editarAsesor(Request $request, $id)
    {
        $result = [];
        try {
            $asesor = Asesores::find($id);
            if ($asesor->identificacion != $request->identificacion) {
                if ($asesor->email != $request->email) {
                    $validator = \Validator::make($request->all(), [
                        'identificacion' => 'required|unique:asesores|max:11',
                        'email' => 'unique:asesores',
                    ]);

                    if ($validator->fails()) {
                        return $validator->errors()->all();
                    }
                } else {
                    $validator = \Validator::make($request->all(), [
                        'identificacion' => 'required|unique:asesores|max:11',
                    ]);

                    if ($validator->fails()) {
                        return $validator->errors()->all();
                    }
                }
            } else if ($asesor->email != $request->email) {
                $validator = \Validator::make($request->all(), [
                    'email' => 'unique:asesores',
                ]);

                if ($validator->fails()) {
                    return $validator->errors()->all();
                }
            }
            $asesor->update($request->all());
            $result['estado'] = true;
            $result['mensaje'] = 'Asesor actulizado satisfactoriamente.';
        } catch (\Exception $exception) {
            $result['estado'] = false;
            $result['mensaje'] = 'No fue posible actualizar al asesor. ' . $exception->getMessage();
        }
        return $result;
    }

    public function cambiarEstadoAsesor(Request $request)
    {
        $result = [];
        try {
            $asesor = Asesores::find($request->id);
            if ($asesor->estado == 'A') {
                $asesor->estado = 'I';
            } else {
                $asesor->estado = 'A';
            }
            $asesor->save();
            $result['estado'] = true;
            $result['mensaje'] = 'Se cambiado el estado satisfactoriamente';
        } catch (\Exception $exception) {
            $result['estado'] = false;
            $result['mensaje'] = 'No fue posible cambiar el estado ' . $exception->getMessage();
        }
        return $result;
    }
}
