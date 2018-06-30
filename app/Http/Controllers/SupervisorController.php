<?php

namespace App\Http\Controllers;

use App\Asesores;
use App\User;
use App\UserAsesor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $supervisores = User::join('role_user', 'users.id', 'role_user.user_id')
            ->where('role_user.role_id', 4)
            ->where('users.empresa_id', Auth::user()->empresa_id)
            ->select('users.*')
            ->get();
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

        $userAsesor = UserAsesor::where('user_id', $id)->get(['asesore_id']);
        $arrayAsesor = [];
        if (count($userAsesor) == 0) {
            $asesores = Asesores::where('empresa_id',Auth::user()->empresa_id)->get();
        } else {
            for ($i = 0; $i < count($userAsesor); $i++) {
                $arrayAsesor[$i] = $userAsesor[$i]->asesore_id;
            }

            $asesores = Asesores::where('empresa_id',Auth::user()->empresa_id)->whereNotIn('id', $arrayAsesor)->get();
        }

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
        $userAsesor = UserAsesor::where('user_id', $id)->get();

        $asesores = Asesores::leftJoin('user_asesors', 'asesores.id', 'user_asesors.asesore_id')->where('user_asesors.user_id', $id)->where('empresa_id',Auth::user()->empresa_id)->get(['asesores.*']);

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
        $userAsesor = new UserAsesor();
        $userAsesor->user_id = $request->idsuper;
        $userAsesor->asesore_id = $request->id;
        $userAsesor->save();
        $result['estado'] = TRUE;
        $result['mensaje'] = 'agregado';
        return $result;
    }

    public function quitarAsesor(Request $request)
    {
        UserAsesor::where('user_id', $request->idsuper)->where('asesore_id', $request->id)->delete();
        $result['estado'] = TRUE;
        $result['mensaje'] = 'Eliminado';
        return $result;
    }

}
