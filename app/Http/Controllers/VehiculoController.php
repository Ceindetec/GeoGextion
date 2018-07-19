<?php

namespace App\Http\Controllers;

use App\Marca;
use App\Vehiculo;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class VehiculoController extends Controller
{
    public function listarVehiculos()
    {
        return view('vehiculos.listarvehiculos');
    }


    public function viewCrearVehiculo()
    {
        $marcas = Marca::pluck('marca', 'id');
        return view('vehiculos.modalcrearvehiculo', compact('marcas'));
    }

    public function crearVehiculo(Request $request)
    {
        $result = [];
        try {
            $validator = \Validator::make($request->all(), [
                'marca_id' => 'required',
                'modelo' => 'required'
            ]);
            if ($validator->fails()) {
                return $validator->errors()->all();
            }

            $existeVehiculo = Vehiculo::where('placa', $request->placa)
                ->where('empresa_id', auth()->user()->empresa_id)
                ->exists();

            if ($existeVehiculo) {
                $result['estado'] = false;
                $result['mensaje'] = 'Este vehiculo ya existe';
                return response()->json($result, 400);
            }

            $vehiculo = new Vehiculo();
            $vehiculo->fill($request->all());
            $vehiculo->empresa_id = auth()->user()->empresa_id;
            $vehiculo->save();

            $result['estado'] = true;
            $result['mensaje'] = 'Trasportador agregado satisfactoriamente.';

        } catch (\Exception $exception) {
            $result['estado'] = false;
            $result['mensaje'] = 'Error de ejecucion. ' . $exception->getMessage();
        }
        return $result;
    }

    public function gridVehiculos()
    {
        $vehiculos = Vehiculo::with('marca')->get();

        return DataTables::of($vehiculos)
            ->addColumn('action', function ($vehiculos) {
                $acciones = '<div class="btn-group">';
                $acciones .= '<a class="btn btn-xs btn-success" data-modal href="' . route('vehiculo.editar', $vehiculos->id) . '">Editar</a>';
                if ($vehiculos->estado == 'A')
                    $acciones .= '<button class="btn btn-xs btn-danger" onclick="cambiarestado(' . $vehiculos->id . ')">Inactivar</button>';
                else
                    $acciones .= '<button class="btn btn-xs btn-success" onclick="cambiarestado(' . $vehiculos->id . ')">Activar</button>';
                $acciones .= '</div>';
                return $acciones;
            })
            ->make(true);
    }


    public function viewEditarVehiculo($id)
    {
        $vehiculo = Vehiculo::findOrFail($id);
        $marcas = Marca::pluck('marca', 'id');
        return view('vehiculos.modaleditarvehiculo', compact('marcas','vehiculo'));
    }

    public function editarVehiculo(Request $request, $id)
    {
        $result = [];
        try {
            $validator = \Validator::make($request->all(), [
                'marca_id' => 'required',
                'modelo' => 'required'
            ]);
            if ($validator->fails()) {
                return $validator->errors()->all();
            }

            $vehiculo = Vehiculo::findOrFail($id);
            $vehiculo->marca_id = $request->marca_id;
            $vehiculo->modelo = $request->modelo;

            if ($vehiculo->placa != $request->placa) {
                $existeVehiculo = Vehiculo::where('placa', $request->placa)
                    ->where('empresa_id', auth()->user()->empresa_id)
                    ->exists();

                if ($existeVehiculo) {
                    $result['estado'] = false;
                    $result['mensaje'] = 'Este vehiculo ya existe';
                    return response()->json($result, 400);
                }
                $vehiculo->placa = $request->placa;
            }
            $vehiculo->save();

            $result['estado'] = true;
            $result['mensaje'] = 'Trasportador agregado satisfactoriamente.';

        } catch (\Exception $exception) {
            $result['estado'] = false;
            $result['mensaje'] = 'Error de ejecucion. ' . $exception->getMessage();
        }
        return $result;
    }

    public function cambiarEstado(Request $request)
    {
        $result = [];
        try {
            $vehiculo = Vehiculo::find($request->id);
            if ($vehiculo->estado == 'A') {
                $vehiculo->estado = 'I';
            } else {
                $vehiculo->estado = 'A';
            }
            $vehiculo->save();
            $result['estado'] = true;
            $result['mensaje'] = 'Se cambiado el estado satisfactoriamente';
        } catch (\Exception $exception) {
            $result['estado'] = false;
            $result['mensaje'] = 'No fue posible cambiar el estado ' . $exception->getMessage();
        }
        return $result;
    }
}
