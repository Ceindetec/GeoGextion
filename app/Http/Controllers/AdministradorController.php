<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
}
