<?php

namespace App\Http\Controllers;

use App\Asesores;
use App\Empresas;
use App\GeoPosicion;
use App\User;
use App\UserAsesor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Yajra\DataTables\DataTables;
use PHPExcel_Worksheet_Drawing;
use Caffeinated\Shinobi\Facades\Shinobi;
use DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Shinobi::isRole('admin')||Shinobi::isRole('sadminempresa')) {
            $asesores = Asesores::select([\DB::raw('concat(nombres," ",apellidos) as nombre, identificacion', 'estado')])
                ->where('estado', 'A')
                ->where('asesores.empresa_id', Auth::user()->empresa_id)
                ->pluck('nombre', 'identificacion')
                ->all();
        } else {
            $asesores = Asesores::join('user_asesors', 'asesores.id', 'user_asesors.asesore_id')
                ->select([\DB::raw('concat(nombres," ",apellidos) as nombre, identificacion', 'estado')])
                ->where('asesores.estado', 'A')
                ->where('user_asesors.user_id', Auth::User()->id)
                ->where('asesores.empresa_id', Auth::user()->empresa_id)
                ->pluck('nombre', 'identificacion')
                ->all();
        }
//        dd($asesores);
        $asesores = array_add($asesores, '', 'Seleccione');
        arsort($asesores);
        return view('home', compact('asesores'));
    }

    public function listaAsesores()
    {
        return view('asesores.listaasesores');
    }

    public function gridAsesores()
    {
        if (Shinobi::isRole('admin')||Shinobi::isRole('sadminempresa')) {
            $asesores = Asesores::where('asesores.empresa_id', Auth::user()->empresa_id);
        } else {
            $asesores = Asesores::join('user_asesors', 'asesores.id', 'user_asesors.asesore_id')
                ->select('asesores.*')
                ->where('asesores.estado', 'A')
                ->where('user_asesors.user_id', Auth::User()->id)
                ->where('asesores.empresa_id', Auth::user()->empresa_id)
                ->get();
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
                'identificacion' => 'required|unique:asesores|max:11',
                'email' => 'unique:asesores',
            ]);

            if ($validator->fails()) {
                return $validator->errors()->all();
            }

            $asesor = new Asesores($request->all());
            $asesor->empresa_id = Auth::user()->empresa_id;
            $asesor->save();
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
        $asesor = Asesores::find($id);
        return view('asesores.modaleditarasesor', compact('asesor'));
    }

    public function editarAsesor(Request $request, $id)
    {
        $result = [];
        try {
            $asesor = Asesores::find($id);
            if ($asesor->identificacion != $request->identificacion) {
                if ($asesor->email != $request->email) {
                    $validator = \Validator::make($request->all(), [
                        'identificacion' => 'required|unique:asesores|max:11',
                        'email' => 'unique:asesores',
                    ]);

                    if ($validator->fails()) {
                        return $validator->errors()->all();
                    }
                } else {
                    $validator = \Validator::make($request->all(), [
                        'identificacion' => 'required|unique:asesores|max:11',
                    ]);

                    if ($validator->fails()) {
                        return $validator->errors()->all();
                    }
                }
            } else if ($asesor->email != $request->email) {
                $validator = \Validator::make($request->all(), [
                    'email' => 'unique:asesores',
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
            $asesor = Asesores::find($request->id);
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

    public function geoPosicionfinal()
    {
        if (Shinobi::isRole('admin')) {
            $markets = Asesores::where('estado', 'A')->get();
        } else {
            $markets = Asesores::join('user_asesors', 'asesores.id', 'user_asesors.asesore_id')
                ->where('user_asesors.user_id', Auth::User()->id)
                ->where('estado', 'A')->get();
        }
        foreach ($markets as $market) {
            $market->getPosition;
        }
        return $markets;
    }

    public function ubicarasesor(Request $request)
    {
        $markets = Asesores::where('estado', 'A')
            ->where('identificacion', $request->identificacion)
            ->first();
        $markets->getPosition;
        return $markets;
    }

    public function rutaasesor(Request $request)
    {
        $markets = Asesores::where('estado', 'A')
            ->where('identificacion', $request->identificacion)->first();
        $markets = $markets->getRuta($request->fecha)->get();

        return $markets;
    }

    public function updatemarketgeneral()
    {
        $response = new StreamedResponse(function () {
            $old_fecha = GeoPosicion::whereDate('fecha', Carbon::now()->format('Y-m-d'))
                ->orderBy('fecha', 'desc')->first();

            while (true) {
                $new_fecha = GeoPosicion::whereDate('fecha', Carbon::now()->format('Y-m-d'))
                    ->orderBy('fecha', 'desc')->first();

                if ($new_fecha->fecha > $old_fecha->fecha) {
                    $markets = Asesores::where('estado', 'A')->get();
                    foreach ($markets as $market) {
                        $market->getPosition;
                    }
                    echo 'data: ' . json_encode($markets) . "\n\n";
                    ob_flush();
                    flush();
                }
                sleep(20);
                $old_fecha = $new_fecha;
            }
        });

        $response->headers->set('Content-Type', 'text/event-stream');
        return $response;
    }

    public function consulta()
    {
        if (Shinobi::isRole('admin')||Shinobi::isRole('sadminempresa')){
            $asesores = Asesores::select([\DB::raw('concat(nombres," ",apellidos) as nombre, identificacion', 'estado')])
                ->where('estado', 'A')
                ->where('asesores.empresa_id', Auth::user()->empresa_id)
                ->pluck('nombre', 'identificacion');
        } else {
            $asesores = Asesores::join('user_asesors', 'asesores.id', 'user_asesors.asesore_id')
                ->select([\DB::raw('concat(nombres," ",apellidos) as nombre, identificacion', 'estado')])
                ->where('asesores.estado', 'A')
                ->where('user_asesors.user_id', Auth::User()->id)
                ->where('asesores.empresa_id', Auth::user()->empresa_id)
                ->pluck('nombre', 'identificacion');
        }
        return view('consulta.consulta', compact('asesores'));
    }

    public function resultadoConsulta(Request $request)
    {
        $geposiciones = GeoPosicion::where('identificacion', $request->asesor)
            ->whereDate('fecha', $request->fecha)
            ->whereBetween('fecha', [$request->fecha . " " . $request->hora1, $request->fecha . " " . $request->hora2])
            ->get();
        return view('consulta.resultado', compact('geposiciones', 'request'));
    }

    public function exportarPdf(Request $request)
    {
        $geposiciones = GeoPosicion::where('identificacion', $request->asesor)
            ->whereDate('fecha', $request->fecha)
            ->whereBetween('fecha', [$request->fecha . " " . $request->hora1, $request->fecha . " " . $request->hora2])
            ->get();
        $data = ['geposiciones' => $geposiciones];
        $pdf = \PDF::loadView('consulta.exportarpdfconsulta', $data);
        $pdf->setPaper('A4', 'landscape');
        return $pdf->download('geoposiciones - ' . Carbon::now()->format('d-m-Y') . '.pdf');
    }

    public function exportarExcel(Request $request)
    {
        $geposiciones = GeoPosicion::where('identificacion', $request->asesor)
            ->whereDate('fecha', $request->fecha)
            ->whereBetween('fecha', [$request->fecha . " " . $request->hora1, $request->fecha . " " . $request->hora2])
            ->get();

        \Excel::create('Geoposiciones', function ($excel) use ($request, $geposiciones) {
            $fecha1 = $request->fecha1;
            $fecha2 = $request->fecha2;
            $rango = $fecha1 . " - " . $fecha2;
            $excel->sheet('Geoposiciones', function ($sheet) use ($geposiciones, $rango) {
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
                ));

                $sheet->setMergeColumn(array(
                    'columns' => array('A'),
                    'rows' => array(
                        array(1, 4),
                    )
                ));

                $sheet->row(1, array('', 'REPORTE DE POSICIONAMIENTO DEL ASESOR'));
                $sheet->row(1, function ($row) {
                    $row->setBackground('#4CAF50');
                });

                $sheet->cells('A1:A4', function ($cells) {
                    $cells->setBackground('#FFFFFF');
                });

                $sheet->setBorder('A1:A4', 'thin');

                $sheet->row(3, array('', 'IDENTIFICACION:', $geposiciones[0]->getAsesor->identificacion, ''));
                $sheet->row(4, array('', 'NOMBRE DEL ASESOR:', $geposiciones[0]->getAsesor->nombres . ' ' . $geposiciones[0]->getAsesor->apellidos, ''));
                $sheet->row(5, array('', 'Fecha GENERACION:', $hoy, ''));

                $fila = 9;
                if (sizeof($geposiciones) > 0) {
                    $sheet->row(8, array('Fecha', 'Coordenadas', 'Direccion'));
                    $sheet->row(8, function ($row) {
                        $row->setBackground('#f2f2f2');
                    });
                    foreach ($geposiciones as $miresul) {
                        $sheet->row($fila,
                            array($miresul->fecha,
                                $miresul->latitud . ', ' . $miresul->longitud,
                                $miresul->direccion
                            ));
                        $fila++;
                    }
                } else
                    $sheet->row($fila, array('No hay resultados'));
                $fila++;
                $fila++;
            });
        })->export('xls');
    }

    public function modalPunto(Request $request)
    {
        $geposicion = GeoPosicion::find($request->id);
        return view('consulta.modalmapapunto', compact('geposicion'));
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

        //dd($asesores);

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


    /**
     * Inicia la seccion de empresa
     */



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
