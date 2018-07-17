<?php

namespace App\Http\Controllers;

use App\Asesor;
use App\Supervisor;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PHPExcel_Worksheet_Drawing;
use Yajra\DataTables\Facades\DataTables;

class SupervisorController extends Controller
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

    public function listaSupervisores()
    {
        return view('supervisores.liestasupervisores');
    }

    public function viewCrearSupervisor()
    {
        return view('supervisores.modalcrearsupervisor');
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
            $supervisor->assignRole(4);
            $result['estado'] = true;
            $result['mensaje'] = 'supervisor agregado satisfactoriamente.';

        } catch (\Exception $exception) {
            $result['estado'] = false;
            $result['mensaje'] = 'Error de ejecucion. ' . $exception->getMessage();
        }
        return $result;
    }

    public function gridSupervisores()
    {
        $supervisores = Supervisor::with('rol')->whereHas('rol',function ($query) {
           $query->where('slug','Super');
        })->where('empresa_id', Auth::user()->empresa_id);

        return DataTables::of($supervisores)
            ->addColumn('action', function ($supervisores) {
                $acciones = '<div class="btn-group">';
                $acciones .= '<a class="btn btn-xs btn-success" data-modal href="' . route('supervisor.editar', $supervisores->id) . '">Editar</a>';
                $acciones .= '<a class="btn btn-xs btn-primary" data-modal="modal-lg" href="' . route('supervisor.asociar', $supervisores->id) . '">Asesores</a>';
                if ($supervisores->estado == 'A')
                    $acciones .= '<button class="btn btn-xs btn-danger" onclick="cambiarestado(' . $supervisores->id . ')">Inactivar</button>';
                else
                    $acciones .= '<button class="btn btn-xs btn-success" onclick="cambiarestado(' . $supervisores->id . ')">Activar</button>';
                $acciones .= '</div>';
                return $acciones;
            })
            ->make(true);
    }

    public function viewEditarSupervisor($id)
    {
        $supervisor = User::find($id);
        return view('supervisores.modaleditarsupervisor', compact('supervisor'));
    }

    public function editarSupervisor(Request $request, $id)
    {
        $result = [];
        try {
            $supervisor = User::find($id);
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

    public function cambiarEstadoSupervisor(Request $request)
    {
        $result = [];
        try {
            $supervisor = User::find($request->id);
            if ($supervisor->estado == 'A') {
                $supervisor->estado = 'I';
            } else {
                $supervisor->estado = 'A';
            }
            $supervisor->save();
            $result['estado'] = true;
            $result['mensaje'] = 'Se cambiado el estado satisfactoriamente';
        } catch (\Exception $exception) {
            $result['estado'] = false;
            $result['mensaje'] = 'No fue posible cambiar el estado ' . $exception->getMessage();
        }
        return $result;
    }

    public function asociarAsesorSupervisor($id)
    {
        $supervisor = User::find($id);
        return view('supervisores.modalasignarasesor', compact('supervisor'));
    }

    public function gridNoAsesores($id)
    {


        $asesores = Asesor::with('rol')->whereHas('rol', function ($query){
            $query->where('roles.slug','asesor');
        })
            ->whereDoesntHave('supervisor', function ($query) use ($id){
                $query->where('supervisor_id','=',$id);
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

    public function gridSiAsesores($id)
    {
        $asesores = Asesor::with('rol')->whereHas('rol', function ($query){
            $query->where('roles.slug','asesor');
        })
            ->whereHas('supervisor', function ($query) use ($id){
                $query->where('supervisor_id','=',$id);
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

    public function agregaAsesor(Request $request)
    {
        $supervisor = Supervisor::findOrFail($request->idsuper);
        $supervisor->asesores()->syncWithoutDetaching($request->id);
        /*$userAsesor = new UserAsesor();
        $userAsesor->user_id = $request->idsuper;
        $userAsesor->asesore_id = $request->id;
        $userAsesor->save();*/
        $result['estado'] = TRUE;
        $result['mensaje'] = 'agregado';
        return $result;
    }

    public function quitarAsesor(Request $request)
    {
        //UserAsesor::where('user_id', $request->idsuper)->where('asesore_id', $request->id)->delete();
        $supervisor = Supervisor::findOrFail($request->idsuper);
        $supervisor->asesores()->detach($request->id);
        $result['estado'] = TRUE;
        $result['mensaje'] = 'Eliminado';
        return $result;
    }


    public function exportar()
    {
        $supervisores = Supervisor::with('rol')->whereHas('rol', function ($query){
            $query->where('slug','super');
        })->where('empresa_id',Auth::user()->empresa_id)->get();

        \Excel::create('Supervisores', function ($excel) use ($supervisores) {

            $excel->sheet('Supervisores', function ($sheet) use ($supervisores) {
                $hoy = Carbon::now();
                $objDrawing = new PHPExcel_Worksheet_Drawing;
                $objDrawing->setPath(public_path('images/logo1.png')); //your image path
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

}
