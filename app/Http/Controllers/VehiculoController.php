<?php

namespace App\Http\Controllers;

use App\Marca;
use App\Vehiculo;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PHPExcel_Worksheet_Drawing;
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

    public function exportarVehiculos()
    {

        $vehiculos = Vehiculo::with('marca')->where('empresa_id',Auth::user()->empresa_id)->get();

        \Excel::create('Transportadores', function ($excel) use ($vehiculos) {

            $excel->sheet('Transportadores', function ($sheet) use ($vehiculos) {
                $hoy = Carbon::now();
                $objDrawing = new PHPExcel_Worksheet_Drawing;
                if(auth()->user()->empresa->logo == null){
                    $objDrawing->setPath(public_path('images/logo1.png')); //your image path
                }else{
                    $objDrawing->setPath(public_path(auth()->user()->empresa->logo)); //your image path
                }
                $objDrawing->setHeight(50);
                $objDrawing->setCoordinates('A1');
                $objDrawing->setWorksheet($sheet);
                $objDrawing->setOffsetY(10);
                $sheet->setWidth(array(
                    'A' => 30,
                    'B' => 30,
                    'C' => 30,
                    'D' => 30,
                    'E' => 30,
                    'F' => 30,
                ));

                $sheet->setMergeColumn(array(
                    'columns' => array('A'),
                    'rows' => array(
                        array(1, 4),
                    )
                ));

                $sheet->row(1, array('', 'REPORTE DE VEHICULOS'));
                $sheet->row(1, function ($row) {
                    $row->setBackground('#4CAF50');
                });

                $sheet->cells('A1:A4', function ($cells) {
                    $cells->setBackground('#FFFFFF');
                });

                $sheet->setBorder('A1:A4', 'thin');


                $sheet->row(4, array('','','','', 'Fecha GENERACION:', $hoy));

                $fila = 7;
                if (sizeof($vehiculos) > 0) {
                    $sheet->row(6, array('Placa', 'Marca', 'Modelo','Estado'));
                    $sheet->row(6, function ($row) {
                        $row->setBackground('#f2f2f2');
                    });


                    foreach ($vehiculos as $miresul) {
                        $sheet->row($fila,
                            array($miresul->placa,
                                $miresul->marca->marca,
                                $miresul->modelo,
                                $miresul->estado
                            ));
                        $fila++;
                    }
                } else
                    $sheet->row($fila, array('No hay resultados'));
                $fila++;
                $fila++;
            });
        })->export('xlsx');
    }
}
