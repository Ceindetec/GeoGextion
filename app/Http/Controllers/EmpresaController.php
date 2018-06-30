<?php

namespace App\Http\Controllers;

use App\Empresas;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

}
