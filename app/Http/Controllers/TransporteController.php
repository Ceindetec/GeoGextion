<?php

namespace App\Http\Controllers;

use App\Trasportador;
use App\User;
use App\Vehiculo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class TransporteController extends Controller
{
    public function listarTrasportadores(){
        return view('transportadores.listatrasportadores');
    }

    public function gridTransportadores(){
        $transportadores = Trasportador::with('rol')->whereHas('rol',function ($query) {
            $query->where('role_id',User::TRASPORTADOR);
        })->where('empresa_id', Auth::user()->empresa_id);

        return DataTables::of($transportadores)
            ->addColumn('action', function ($transportadores) {
                $acciones = '<div class="btn-group">';
                $acciones .= '<a class="btn btn-xs btn-success" data-modal href="' . route('transportadores.editar', $transportadores->id) . '">Editar</a>';
                if ($transportadores->estado == 'A')
                    $acciones .= '<button class="btn btn-xs btn-danger" onclick="cambiarestado(' . $transportadores->id . ')">Inactivar</button>';
                else
                    $acciones .= '<button class="btn btn-xs btn-success" onclick="cambiarestado(' . $transportadores->id . ')">Activar</button>';
                $acciones .= '</div>';
                return $acciones;
            })
            ->make(true);
    }

    public function viewCrearTransportador(){
        $vehiculos = Vehiculo::pluck('placa','id');
        return view('transportadores.modalcreartrasportador', compact('vehiculos'));
    }

    public function crearTransportador(Request $request){
        $result = [];
        try {
            $validator = \Validator::make($request->all(), [
                'identificacion' => 'required|unique:users|max:11',
                'email' => 'required|unique:users',
            ]);

            if ($validator->fails()) {
                return $validator->errors()->all();
            }

            $transportador = new Trasportador();
            $transportador->name = $request->nombres;
            $transportador->email = $request->email;
            $transportador->identificacion = trim($request->identificacion);
            $transportador->nombres = trim($request->nombres);
            $transportador->apellidos = trim($request->apellidos);
            $transportador->telefono = $request->telefono;
            $transportador->password = bcrypt(trim($request->identificacion));
            $transportador->empresa_id = Auth::user()->empresa_id;
            $transportador->vehiculo_id = $request->vehiculo_id;
            $transportador->save();
            $transportador->assignRole(User::TRASPORTADOR);
            $result['estado'] = true;
            $result['mensaje'] = 'Trasportador agregado satisfactoriamente.';

        } catch (\Exception $exception) {
            $result['estado'] = false;
            $result['mensaje'] = 'Error de ejecucion. ' . $exception->getMessage();
        }
        return $result;
    }


    public function viewEditarTransportador($id)
    {
        $transportador = User::find($id);
        $vehiculos = Vehiculo::pluck('placa','id');
        return view('transportadores.modaleditartrasportador', compact('transportador','vehiculos'));
    }

    public function editarTransportador(Request $request, $id)
    {
        $result = [];
        try {
            $transportado = Trasportador::find($id);
            if ($transportado->identificacion != $request->identificacion) {
                if ($transportado->email != $request->email) {
                    $validator = \Validator::make($request->all(), [
                        'identificacion' => 'required|unique:users|max:11',
                        'email' => 'required|unique:users',
                    ]);
                    if ($validator->fails()) {
                        return $validator->errors()->all();
                    }
                } else {
                    $validator = \Validator::make($request->all(), [
                        'identificacion' => 'required|unique:users|max:11',
                    ]);

                    if ($validator->fails()) {
                        return $validator->errors()->all();
                    }
                }
            } else if ($transportado->email != $request->email) {
                $validator = \Validator::make($request->all(), [
                    'email' => 'required|unique:users',
                ]);

                if ($validator->fails()) {
                    return $validator->errors()->all();
                }
            }
            $transportado->fill($request->all());
            $transportado->save();
            $result['estado'] = true;
            $result['mensaje'] = 'supervisor actulizado satisfactoriamente.';
        } catch (\Exception $exception) {
            $result['estado'] = false;
            $result['mensaje'] = 'No fue posible actualizar al supervisor. ' . $exception->getMessage();
        }
        return $result;
    }
}
