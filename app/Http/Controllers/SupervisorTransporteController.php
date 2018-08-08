<?php

namespace App\Http\Controllers;

use App\Http\Middleware\SupervisorTrasnporte;
use App\SupervisorTransporte;
use App\Trasportador;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PHPExcel_Worksheet_Drawing;
use Yajra\DataTables\Facades\DataTables;

class SupervisorTransporteController extends Controller
{
    public function listaSupervisoresTransporte()
    {
        return view('supertransporte.listarsupertransporte');
    }

    public function gridSupervisoresTrasporte()
    {
        $supervisores = SupervisorTransporte::with('rol')->whereHas('rol',function ($query) {
            $query->where('role_id',User::SUPERVISRORT);
        })->where('empresa_id', Auth::user()->empresa_id);

        return DataTables::of($supervisores)
            ->addColumn('action', function ($supervisores) {
                $acciones = '<div class="btn-group">';
                $acciones .= '<a class="btn btn-xs btn-success" data-modal href="' . route('supervisortransporte.editar', $supervisores->id) . '">Editar</a>';
                $acciones .= '<a class="btn btn-xs btn-primary" data-modal="modal-lg" href="' . route('supervisortransporte.asociar', $supervisores->id) . '">Trasportadores</a>';
                if ($supervisores->estado == 'A')
                    $acciones .= '<button class="btn btn-xs btn-danger" onclick="cambiarestado(' . $supervisores->id . ')">Inactivar</button>';
                else
                    $acciones .= '<button class="btn btn-xs btn-success" onclick="cambiarestado(' . $supervisores->id . ')">Activar</button>';
                $acciones .= '</div>';
                return $acciones;
            })
            ->make(true);
    }

    public function viewCrearSupervisor()
    {
        return view('supertransporte.modalcrearsupervisor');
    }

    public function crearSupervisor(Request $request)
    {
        $result = [];
        try {
            $validator = \Validator::make($request->all(), [
                'identificacion' => 'required|unique:users|max:11',
                'email' => 'required|unique:users',
            ]);

            if ($validator->fails()) {
                return $validator->errors()->all();
            }

            $supervisor = new User();
            $supervisor->name = $request->nombres;
            $supervisor->email = $request->email;
            $supervisor->identificacion = trim($request->identificacion);
            $supervisor->nombres = trim($request->nombres);
            $supervisor->apellidos = trim($request->apellidos);
            $supervisor->telefono = $request->telefono;
            $supervisor->password = bcrypt(trim($request->identificacion));
            $supervisor->empresa_id = Auth::user()->empresa_id;
            $supervisor->save();
            $supervisor->assignRole(User::SUPERVISRORT);
            $result['estado'] = true;
            $result['mensaje'] = 'supervisor agregado satisfactoriamente.';

        } catch (\Exception $exception) {
            $result['estado'] = false;
            $result['mensaje'] = 'Error de ejecucion. ' . $exception->getMessage();
        }
        return $result;
    }


    public function viewEditarSupervisorTransporte($id)
    {
        $supervisor = SupervisorTransporte::find($id);
        return view('supertransporte.modaleditarsuperivsor', compact('supervisor'));
    }

    public function editarSupervisorTransporte(Request $request, $id)
    {
        $result = [];
        try {
            $supervisor = SupervisorTransporte::find($id);
            if ($supervisor->identificacion != $request->identificacion) {
                if ($supervisor->email != $request->email) {
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
            } else if ($supervisor->email != $request->email) {
                $validator = \Validator::make($request->all(), [
                    'email' => 'required|unique:users',
                ]);

                if ($validator->fails()) {
                    return $validator->errors()->all();
                }
            }
            $supervisor->update($request->all());
            $result['estado'] = true;
            $result['mensaje'] = 'supervisor actulizado satisfactoriamente.';
        } catch (\Exception $exception) {
            $result['estado'] = false;
            $result['mensaje'] = 'No fue posible actualizar al supervisor. ' . $exception->getMessage();
        }
        return $result;
    }

    public function exportar()
    {
        $supervisores = SupervisorTransporte::with('rol')->whereHas('rol', function ($query){
            $query->where('role_id',User::SUPERVISRORT);
        })->where('empresa_id',Auth::user()->empresa_id)->get();

        \Excel::create('SupervisoresTransporte', function ($excel) use ($supervisores) {

            $excel->sheet('SupervisoresTransporte', function ($sheet) use ($supervisores) {
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

                $sheet->row(1, array('', 'REPORTE DE SUPERVISORES'));
                $sheet->row(1, function ($row) {
                    $row->setBackground('#4CAF50');
                });

                $sheet->cells('A1:A4', function ($cells) {
                    $cells->setBackground('#FFFFFF');
                });

                $sheet->setBorder('A1:A4', 'thin');


                $sheet->row(4, array('','','','', 'Fecha GENERACION:', $hoy));

                $fila = 7;
                if (sizeof($supervisores) > 0) {
                    $sheet->row(6, array('Identificacion', 'Nombres', 'Apellidos','Email','Telefono','Estado'));
                    $sheet->row(6, function ($row) {
                        $row->setBackground('#f2f2f2');
                    });


                    foreach ($supervisores as $miresul) {
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

    public function asociarTrasportadorSupervisor($id)
    {
        try{
            $supervisor = SupervisorTransporte::query()->findOrFail($id);
            return view('supertransporte.modalasignartransportador', compact('supervisor'));
        }catch (\Exception $exception){
           return abort(404);
        }
    }

    public function gridNoTransportador($id)
    {


        $asesores = Trasportador::with('rol')->whereHas('rol', function ($query){
            $query->where('roles.id',User::TRASPORTADOR);
        })
            ->whereDoesntHave('supertransportador', function ($query) use ($id){
                $query->where('supertransporte_id','=',$id);
            })
            ->where('empresa_id',Auth::user()->empresa_id)->get();


        return DataTables::of($asesores)
            ->addColumn('action', function ($asesores) {
                $acciones = '<div class="btn-group">';
                $acciones .= '<button class="btn btn-xs btn-success" onclick="agregar(' . $asesores->id . ')"><i class="fa fa-plus-square" aria-hidden="true"></i></button>';
                $acciones .= '</div>';
                return $acciones;
            })
            ->make(true);
    }

    public function gridSiTransportador($id)
    {
        $asesores = Trasportador::with('rol')->whereHas('rol', function ($query){
            $query->where('roles.id',User::TRASPORTADOR);
        })
            ->whereHas('supertransportador', function ($query) use ($id){
                $query->where('supertransporte_id','=',$id);
            })
            ->where('empresa_id',Auth::user()->empresa_id)->get();

        return DataTables::of($asesores)
            ->addColumn('action', function ($asesores) {
                $acciones = '<div class="btn-group">';
                $acciones .= '<button class="btn btn-xs btn-danger" onclick="quitar(' . $asesores->id . ')"><i class="fa fa-minus-square" aria-hidden="true"></i></button>';
                $acciones .= '</div>';
                return $acciones;
            })
            ->make(true);
    }


    public function agregaTransportador(Request $request)
    {
        $supervisor = SupervisorTransporte::query()->findOrFail($request->idsuper);
        $supervisor->transportadores()->syncWithoutDetaching($request->id);
        $result['estado'] = TRUE;
        $result['mensaje'] = 'agregado';
        return $result;
    }

    public function quitarTransportador(Request $request)
    {
        //UserAsesor::where('user_id', $request->idsuper)->where('asesore_id', $request->id)->delete();
        $supervisor = SupervisorTransporte::query()->findOrFail($request->idsuper);
        $supervisor->transportadores()->detach($request->id);
        $result['estado'] = TRUE;
        $result['mensaje'] = 'Eliminado';
        return $result;
    }

}
