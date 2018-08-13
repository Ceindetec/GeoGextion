<?php

namespace App\Http\Controllers;

use App\Empresas;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Excel;
use PHPExcel_Worksheet_Drawing;
use Yajra\DataTables\Facades\DataTables;

class EmpresaController extends Controller
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


    public function listaEmpresas()
    {
        return view('empresas.listaempresas');
    }

    public function viewCrearEmpresa()
    {
        return view('empresas.modalcrearempresa');
    }

    public function crearEmpresa(Request $request)
    {
        $result = [];
        DB::beginTransaction();
        try {
            $validator = \Validator::make($request->all(), [
                'nit' => 'required|unique:empresas|max:15',
                'email' => 'required|unique:users',
            ]);

            if ($validator->fails()) {
                return $validator->errors()->all();
            }

            $empresa = new Empresas($request->all());
            $empresa->nit = trim($request->nit);
            $empresa->save();
            $user = new User($request->all());
            $user->name = trim($request->nombres);
            $user->email = trim($request->email);
            $user->password = bcrypt(trim($request->identificacion));
            $user->empresa_id = $empresa->id;
            $user->save();
            $user->assignRole(2);
            DB::commit();
            $result['estado'] = true;
            $result['mensaje'] = 'Empresa creada satisfactoriamente.';

        } catch (\Exception $exception) {
            DB::rollBack();
            $result['estado'] = false;
            $result['mensaje'] = 'Error de ejecucion. ' . $exception->getMessage();
        }
        return $result;
    }



    public function gridEmpresas()
    {
        $empresas = Empresas::all();
        return DataTables::of($empresas)
            ->addColumn('action', function ($empresas) {
                $acciones = '<div class="btn-group">';
                $acciones .= '<a class="btn btn-xs btn-success" data-modal href="' . route('empresa.editar', $empresas->id) . '">Editar</a>';
                if ($empresas->estado == 'A')
                    $acciones .= '<button class="btn btn-xs btn-danger" onclick="cambiarestado(' . $empresas->id . ')">Inactivar</button>';
                else
                    $acciones .= '<button class="btn btn-xs btn-success" onclick="cambiarestado(' . $empresas->id . ')">Activar</button>';
                $acciones .= '</div>';
                return $acciones;
            })
            ->make(true);
    }

    public function cambiarEstadoEmpresa(Request $request)
    {
        $result = [];
        try {
            $empresa = Empresas::find($request->id);
            if ($empresa->estado == 'A') {
                $empresa->estado = 'I';
            } else {
                $empresa->estado = 'A';
            }
            $empresa->save();
            $result['estado'] = true;
            $result['mensaje'] = 'Se cambiado el estado satisfactoriamente';
        } catch (\Exception $exception) {
            $result['estado'] = false;
            $result['mensaje'] = 'No fue posible cambiar el estado ' . $exception->getMessage();
        }
        return $result;
    }

    public function viewEditarEmpresa($id)
    {
        $empresa = Empresas::join('users', 'users.empresa_id', 'empresas.id')
            ->join('role_user', 'users.id', 'role_user.user_id')
            ->where('role_user.role_id', 2)
            ->where('empresas.id', $id)
            ->select('empresas.*','users.identificacion','users.email','users.nombres','users.apellidos')
            ->first();
//        return $empresa;
        return view('empresas.modaleditarempresa', compact('empresa'));
    }

    public function editarEmpresa(Request $request, $id)
    {
        $result = [];
        DB::beginTransaction();
        try {
            $empresa = Empresas::find($id);
            $superAdminEmpresa = User::where("identificacion",$request->identificacion)->first();

            $validator = \Validator::make($request->all(), [
                'identificacion' => 'required|unique:users,identificacion,'.$superAdminEmpresa->id.'|max:11',
                'email' => 'required|unique:users,email,'.$superAdminEmpresa->id,
                'nit' => 'required|unique:empresas,nit,'.$id
            ]);
            if ($validator->fails()) {
                return $validator->errors()->all();
            }

            $superAdminEmpresa->update($request->all());
            $empresa->update($request->all());
            DB::commit();
            $result['estado'] = true;
            $result['mensaje'] = 'Los datos de la empresa se actualizo satisfactoriamente.';
        } catch (\Exception $exception) {
            DB::rollBack();
            $result['estado'] = false;
            $result['mensaje'] = 'No fue posible actualizar la empresa. ' . $exception->getMessage();
        }
        return $result;
    }


    public function exportar()
    {
        $empresas = Empresas::with(['user'=>function($query){
            $query->with(['rol'=>function($query){
                $query->where('slug','sadminempresa');
            }])->whereHas('rol',function($query){
                $query->where('slug','sadminempresa');
            });
        }])->get();

        //dd($empresas);

        \Excel::create('Empresas', function ($excel) use ($empresas) {

            $excel->sheet('Empresas', function ($sheet) use ($empresas) {
                $hoy = Carbon::now();
                $objDrawing = new PHPExcel_Worksheet_Drawing;
                $objDrawing->setPath('images/logo1.png'); //your image path
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

                $sheet->row(1, array('', 'REPORTE DE EMPRESAS'));
                $sheet->row(1, function ($row) {
                    $row->setBackground('#4CAF50');
                });

                $sheet->cells('A1:A4', function ($cells) {
                    $cells->setBackground('#FFFFFF');
                });

                $sheet->setBorder('A1:A4', 'thin');


                $sheet->row(4, array('','','','', 'Fecha GENERACION:', $hoy));

                $fila = 7;
                if (sizeof($empresas) > 0) {
                    $sheet->row(6, array('Nit', 'Razon social', 'Representante','Telefono','Direccion','Estado'));
                    $sheet->row(6, function ($row) {
                        $row->setBackground('#f2f2f2');
                    });


                    foreach ($empresas as $miresul) {
                        $sheet->row($fila,
                            array($miresul->nit,
                                $miresul->razon,
                                $miresul->user[0]->nombres.' '.$miresul->user[0]->apellidos,
                                $miresul->telefono,
                                $miresul->direccion,
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
