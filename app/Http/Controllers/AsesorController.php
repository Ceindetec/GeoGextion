<?php

namespace App\Http\Controllers;

use App\Asesor;
use App\Asesores;
use App\User;
use Caffeinated\Shinobi\Facades\Shinobi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PHPExcel_Worksheet_Drawing;
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
        if (Shinobi::isRole('admin') || Shinobi::isRole('sadminempresa')) {

            $asesores = User::query()->whereHas('rol', function ($query) {
                $query->where('slug', 'asesor');
            })
                ->where('empresa_id', auth()->user()->empresa_id)->get();

        } else {
            $id = Auth::User()->id;
            $asesores = Asesor::with('rol')->whereHas('rol', function ($query) {
                $query->where('roles.slug', 'asesor');
            })
                ->whereHas('supervisor', function ($query) use ($id) {
                    $query->where('supervisor_id', '=', $id);
                })
                ->where('empresa_id', Auth::user()->empresa_id)->get();

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
                'identificacion' => 'required|unique:users|max:11',
                'email' => 'unique:users',
            ]);

            if ($validator->fails()) {
                return $validator->errors()->all();
            }
            $asesor = new User($request->all());
            $asesor->name = $request->nombres;
            $asesor->password = bcrypt($request->identificacion);
            $asesor->empresa_id = Auth::user()->empresa_id;
            $asesor->save();
            $user = User::find($asesor->id);
            $user->assignRole(Asesor::ASESOR);
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
        $asesor = User::find($id);
        return view('asesores.modaleditarasesor', compact('asesor'));
    }

    public function editarAsesor(Request $request, $id)
    {
        $result = [];
        try {
            $asesor = User::find($id);
            if ($asesor->identificacion != $request->identificacion) {
                if ($asesor->email != $request->email) {
                    $validator = \Validator::make($request->all(), [
                        'identificacion' => 'required|unique:users|max:11',
                        'email' => 'unique:users',
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
            } else if ($asesor->email != $request->email) {
                $validator = \Validator::make($request->all(), [
                    'email' => 'unique:users',
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
            $asesor = User::find($request->id);
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


    public function exportar()
    {
        $asesores = Asesor::with('rol')->whereHas('rol', function ($query) {
            $query->where('slug', 'asesor');
        })->where('empresa_id', Auth::user()->empresa_id)->get();

        \Excel::create('Asesores', function ($excel) use ($asesores) {

            $excel->sheet('Asesores', function ($sheet) use ($asesores) {
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
                    'F' => 30
                ));

                $sheet->setMergeColumn(array(
                    'columns' => array('A'),
                    'rows' => array(
                        array(1, 4),
                    )
                ));

                $sheet->row(1, array('', 'REPORTE DE ASESORES'));
                $sheet->row(1, function ($row) {
                    $row->setBackground('#4CAF50');
                });

                $sheet->cells('A1:A4', function ($cells) {
                    $cells->setBackground('#FFFFFF');
                });

                $sheet->setBorder('A1:A4', 'thin');


                $sheet->row(4, array('', '', '', '', 'Fecha GENERACION:', $hoy));

                $fila = 7;
                if (sizeof($asesores) > 0) {
                    $sheet->row(6, array('Identificacion', 'Nombres', 'Apellidos', 'Email', 'Telefono', 'Estado'));
                    $sheet->row(6, function ($row) {
                        $row->setBackground('#f2f2f2');
                    });


                    foreach ($asesores as $miresul) {
                        $sheet->row($fila,
                            array($miresul->identificacion,
                                $miresul->nombres,
                                $miresul->apellidos,
                                $miresul->email,
                                $miresul->telefono,
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
