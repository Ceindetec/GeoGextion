<?php

namespace App\Http\Controllers;

use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PHPExcel_Worksheet_Drawing;
use Yajra\DataTables\Facades\DataTables;

class AdministradorController extends Controller
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

    public function listaAdministradores()
    {
        return view('admins.listaadmins');
    }

    public function viewCrearAdministrador()
    {
        return view('admins.modalcrearadmin');
    }

    public function gridAdministradores()
    {
        $admins = User::join('role_user', 'users.id', 'role_user.user_id')
            ->where('role_user.role_id', 3)
            ->where('users.empresa_id', Auth::user()->empresa_id)
            ->select('users.*')
            ->get();
        return DataTables::of($admins)
            ->addColumn('action', function ($admins) {
                $acciones = '<div class="btn-group">';
                $acciones .= '<a class="btn btn-xs btn-success" data-modal href="' . route('administrador.editar', $admins->id) . '">Editar</a>';
                if ($admins->estado == 'A')
                    $acciones .= '<button class="btn btn-xs btn-danger" onclick="cambiarestado(' . $admins->id . ')">Inactivar</button>';
                else
                    $acciones .= '<button class="btn btn-xs btn-success" onclick="cambiarestado(' . $admins->id . ')">Activar</button>';
                $acciones .= '</div>';
                return $acciones;
            })
            ->make(true);
    }


    public function crearAdministrador(Request $request)
    {
        $result = [];
        try {
            $validator = \Validator::make($request->all(), [
                'identificacion' => 'required|unique:asesores|max:11',
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
            $supervisor->assignRole(3);
            $result['estado'] = true;
            $result['mensaje'] = 'Administrador agregado satisfactoriamente.';

        } catch (\Exception $exception) {
            $result['estado'] = false;
            $result['mensaje'] = 'Error de ejecucion. ' . $exception->getMessage();
        }
        return $result;
    }

    public function exportar()
    {
        $administradores = User::with('rol')->whereHas('rol', function ($query){
            $query->where('slug','admin');
        })->where('empresa_id',Auth::user()->empresa_id)->get();

        \Excel::create('Administradores', function ($excel) use ($administradores) {

            $excel->sheet('Administradores', function ($sheet) use ($administradores) {
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

                $sheet->row(1, array('', 'REPORTE DE ADMINISTRADORES'));
                $sheet->row(1, function ($row) {
                    $row->setBackground('#4CAF50');
                });

                $sheet->cells('A1:A4', function ($cells) {
                    $cells->setBackground('#FFFFFF');
                });

                $sheet->setBorder('A1:A4', 'thin');


                $sheet->row(4, array('','','','', 'Fecha GENERACION:', $hoy));

                $fila = 7;
                if (sizeof($administradores) > 0) {
                    $sheet->row(6, array('Identificacion', 'Nombres', 'Apellidos','Email','Telefono','Estado'));
                    $sheet->row(6, function ($row) {
                        $row->setBackground('#f2f2f2');
                    });


                    foreach ($administradores as $miresul) {
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
