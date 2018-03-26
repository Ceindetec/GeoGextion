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

    private function consultaGeo($identificacion,$fecha1,$fecha2,$hora1,$hora2){

        $geposiciones = GeoPosicion::where('identificacion', $identificacion)
            ->whereDate('fecha',">" ,$fecha1)
            ->whereDate('fecha',"<" ,$fecha2)
            ->whereTime('fecha', '>=', $hora1)
            ->whereTime('fecha', '<', $hora2)
            ->get();
        return $geposiciones;
    }


    public function resultadoConsulta(Request $request)
    {
        $fecha = explode(" - ",$request->fecha);
        $geposiciones = $this->consultaGeo($request->asesor,$fecha[0],$fecha[1],$request->hora1.':00',$request->hora2.':00');

//        return $geposiciones;
        return view('consulta.resultado', compact('geposiciones', 'request'));
    }

    public function exportarPdf(Request $request)
    {
        $fecha = explode(" - ",$request->fecha);
        $geposiciones = $this->consultaGeo($request->asesor,$fecha[0],$fecha[1],$request->hora1.':00',$request->hora2.':00');
        $data = ['geposiciones' => $geposiciones];
        $pdf = \PDF::loadView('consulta.exportarpdfconsulta', $data);
        $pdf->setPaper('A4', 'landscape');
        return $pdf->download('geoposiciones - ' . Carbon::now()->format('d-m-Y') . '.pdf');
    }

    public function exportarExcel(Request $request)
    {
        $fecha = explode(" - ",$request->fecha);
        $geposiciones = $this->consultaGeo($request->asesor,$fecha[0],$fecha[1],$request->hora1.':00',$request->hora2.':00');

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


    public function insertlocoDuitama(){

 //       $dias = [2,4,5,8,9,11,15,16,18,19,22,23,25,26,30,31];
//        $dias = [1,2,5,6,8,9,12,14,15,16,19,21,22,23,26,28,];
        $dias = [2,5,4,8,9,11,15,16,18,19,22];

        $identificaciones = ["79621679","1030556368","1026571416"];

        $ruta1 = [["5.826884",	"-73.031006",  "Cra. 18 #17-8 a 17-112"],
            ["5.825961",	"-73.032774",  "Cra. 18 #15-2 a 15-106"],
            ["5.824349",	"-73.035705",  "Cra. 18 #11-2 a 11-90"],
            ["5.824274",	"-73.035705",  "Cra. 17 #9-53 a 9-111"],
            ["5.827715",	"-73.036327",  "Cra. 14 #14-50"],
            ["5.827295",	"-73.041703",  "Cra. 11 #7-1 a 7-73"],
            ["5.827411",	"-73.04536", "Cl. 6 #8-1 a 8-103"],
            ["5.824762",	"-73.046744",  "Cl. 2 #8-2 a 8-212"],
            ["5.822971",	"-73.044007",  "Cl. 1 #12-52 a 12-108"],
            ["5.822297",	"-73.042407",  "Cra. 17 #2o-1 a 2o-11"],
            ["5.819083",	"-73.040056",  "Cl. 2 #20-86 a 20-162"],
            ["5.817699",	"-73.038403",  "Cl. 3 #24-80"],
            ["5.81615" ,    "-73.035259",  "Cl. 7"],
            ["5.814408",	"-73.033251",  "Cra 28B"],
            ["5.809243",	"-73.029743",  "Cl. 8 #37-2 a 37-48"],
            ["5.807955",	"-73.031771",  "Cl. 6A"],
            ["5.806757",	"-73.033975",  "Cl. 1a #42-2 a 42-62"],
            ["5.803598",	"-73.034641",  "Cl. 1a #42-2 a 42-62"],
            ["5.803543",	"-73.033546",  "Cl. 1a #42-2"],
            ["5.803543",	"-73.033546",  "Cl. 1a #42-2"],
            ["5.803543",	"-73.033546",  "Cl. 1a #42-2"],
            ["5.803543",	"-73.033546",  "Cl. 1a #42-2"]];
        $ruta2 = [["5.815671612"   , "-73.0310262    ","Tv. 30 #10-1 a 10-63"],
            ["5.815799669"    , "-73.03148221","Cra 28B #10-2 a 10-52"],
            ["5.815799669"    , "-73.03148221","Cra 28B #10-2 a 10-52"],
            ["5.816071846"    , "-73.03183358","Cl. 10 #28-1 a 28-59"],
            ["5.816199929"    , "-73.03242635","Cl. 9a #27-2 a 27-114"],
            ["5.815906405"    , "-73.03174775","Cl. 10 #28a-2 a 28a-38"],
            ["5.815869048"    , "-73.03136419","Cra 28B #10-2 a 10-52"],
            ["5.815858374"    , "-73.02986484","Cl. 12"],
            ["5.815548839"    , "-73.0286498","Cl. 14 #30a-1 a 30a-71"],
            ["5.815548839"    , "-73.0286498","Cl. 14 #30a-1 a 30a-71"],
            ["5.815073863"    , "-73.02718799","Cl. 15 #32-2 a 32-44"],
            ["5.814353393"    , "-73.02780222","Cra. 33 #16-4"],
            ["5.812720323"    , "-73.02887779","Cl. 12 #33-3 a 33-103"],
            ["5.811627557"    , "-73.0289663","Cra. 35 #10-65 a 10-79"],
            ["5.81171623"     , "-73.02785575","Cra. 35 #12-70"],
            ["5.812431367"    , "-73.02597821","Cra. 35 #13A-250"],
            ["5.812431367"    , "-73.02597821","Cra. 35 #13A-250"],
            ["5.812431367"    , "-73.02597821","Cra. 35 #13A-250"],
            ["5.812917019"    , "-73.02505016","Trasnversal 35"],
            ["5.81284764"     , "-73.02371174","Cra 40 A #18-1 a 18-271"],
            ["5.81274624"     , "-73.02216411","Cra 40 A #18-1 a 18-271"],
            ["5.812879661"    , "-73.02150697","Cra. 37 #19-2 a 19-52"]];
        $ruta3 = [["5.838021717",	"-73.03687036",	"Cra. 5 #15b-2 a 15b-108"],
           ["5.838790186",	"-73.03687036",	"Cra. 3A #15-"],
           ["5.839601347",	"-73.03919852",	"Cra. 2b #15a-8"],
           ["5.839601347",	"-73.03775013",	"Cra. 2b #15a-8"],
           ["5.84079674",	"-73.03775013",	"Cra. 2A #16-2 a 16-9"],
           ["5.841543859",	"-73.03655923",	"Cl. 18 #2a-1 a 2a-2"],
           ["5.8417146289183",	"-73.03521812",	"Cl. 2"],
           ["5.8407327010338",	"-73.03349078",	"Cl. 2"],
           ["5.8384699910944",	"-73.03273976",	"ra. 5 #22-1 a 22-9"],
           ["5.836804972",	"-73.03164542",	"Cl. 20 #8-1 a 8-5"],
           ["5.834755712",	"-73.03222478",	"Cra. 10 #20-2 a 20-2"],
           ["5.83347492",	"-73.03224624",	"Av. Circunvala"],
           ["5.83347492",	"-73.03053267",	"Av. Circunvala"],
           ["5.83347492",	"-73.03053267",	"Av. Circunvala"],
           ["5.832492978",	"-73.03053267",	"Cl. 23 #16-1 a 16-10"],
           ["5.831809886",	"-73.02930958",	"Cl. 26 #16-2 a 16-4"],
           ["5.831084101",	"-73.02731402",	"ra. 16a #22-2 a 22-6"],
           ["5.829589834",	"-73.02746422",	"Cra. 17 #21-2 a 21-13"],
           ["5.828351724",	"-73.02776463",	"Cra. 17 #20a-2 a 20a-2"],
           ["5.826430513",	"-73.02911646",	"Cra. 19 #18-2 a 18-11"],
           ["5.826430513",	"-73.03036101",	"Cra. 19 #18-2 a 18-11"],
           ["5.826430513",	"-73.03036101",	"Cra. 19 #18-2 a 18-11"]];
        $ruta4 = [["5.770435"	, "-73.062948","Calle Pantano de Vargas, Paipa, Boyacá"],
           ["5.76806"  , "-73.059907","Calle Pantano de Vargas, Paipa, Boyacá"],
           ["5.771271"	, "-73.054934",""],
           ["5.773858"	, "-73.050781","Vía Tibasosa, Tibasosa, Boyacá"],
           ["5.779622"	, "-73.053231",""],
           ["5.793277"	, "-73.044592",""],
           ["5.806554"	, "-73.036264","Cra. 35 #3o-10 a 3o-58, Duitama, Boyacá"],
           ["5.815875"	, "-73.030113","Cl. 12, Duitama, Boyacá"],
           ["5.817756"	, "-73.027717","Cl. 16 #30-2 a 30-136, Duitama"],
           ["5.815312"	, "-73.026252","Cra. 33 #15a-40 a 15a-94, Duitama, Boyacá"],
           ["5.815995"	, "-73.028832","Cl. 14 #30a-2 a 30a-72, Duitama, Boyacá"],
           ["5.820925"	, "-73.032436","Cl. 12a #22-30 a 22-82, Duitama, Boyacá"],
           ["5.825642"	, "-73.03507" ,"Cl. 13 #16-2 a 16-102, Duitama, Boyacá"],
           ["5.829389"	, "-73.033453","Cl. 17 #14-2 a 14-108, Duitama, Boyacá"],
           ["5.829656"	, "-73.028259","Cra. 17 #21-2 a 21-130, Duitama, Boyacá"],
           ["5.833379"	, "-73.026479","Cra. 16a #26-2 a 26-192, Duitama, Boyacá"],
           ["5.83382"  , "-73.027358","Cl. 27 #10-1 a 10-15, Duitama, Boyacá"],
           ["5.832136"	, "-73.029965","Cl. 23 #13-2 a 13-18, Duitama, Boyacá"],
           ["5.83274"  , "-73.038171","Cra. 8a #13-2 a 13-104, Duitama, Boyacá"],
           ["5.834981"	, "-73.035322","Cl 17A #7b-2 a 7b-18, Duitama, Boyacá"],
           ["5.840175"	, "-73.035895","Cl. 17 #3-2 a 3-140, Duitama, Boyacá"],
           ["5.844051"	, "-73.035909","Cl. 18 #1e-2 a 1e-78, Duitama, Boyacá"]];
        $ruta5 = [["5.825351698"	, "-73.03431183",	"Cra. 18 #13-9 a 13-91"],
            ["5.826120185"	, "-73.03707987",	"Cl. 11 #16-42"],
            ["5.826376347"	, "-73.0395475",	"Cra. 13 #8-2 a 8-106"],
            ["5.825735942"	, "-73.04115683",	"Cl. 7 #11b-2 a 11b-72"],
            ["5.825864023"	, "-73.04225117",	"Cra. 11a #6-1 a 6-69"],
            ["5.826376347"	, "-73.04381758",	"Cra. 10 #4-2 a 4-92"],
            ["5.826632508"	, "-73.04474026",	"Cra. 9 #4-2 a 4-56"],
            ["5.826931364"	, "-73.04596335",	"Cra. 8 #4-18 a 4-110"],
            ["5.826461734"	, "-73.0465427",	"Cl. 3a #7-31"],
            ["5.825138229"	, "-73.04652125",	"Cl. 2a #8-2 a 8-210"],
            ["5.824198966"	, "-73.04611355",	"Cl. 1 #8-72"],
            ["5.82368664"	, "-73.0452767",	"Cl. 1 #9a-1 a 9a-45"],
            ["5.822576599"	, "-73.04448277",	"Cl. 2o #10-1 a 10-49"],
            ["5.822192354"	, "-73.04381758",	"Cra. 16, Duitama"],
            ["5.821210392"	, "-73.04134995",   "Cra. 18, Duitama"],
            ["5.819886875"	, "-73.03956896",	"Cl. 4 #19-2 a 19-112"],
            ["5.818734132"	, "-73.03718716",	"Cra. 23 #5-1 a 5-111"],
            ["5.81732522"	, "-73.03626448",	"Cra. 25 #4-77"],
            ["5.813183855"	, "-73.02963406",	"Cra 32, Duitama"],
            ["5.811988403"	, "-73.02901179",	"Cl. 11 #33-2 a 33-102"],
            ["5.808957069"	, "-73.0266729",	"Cl. 13 #38-23"],
            ["5.807121188"	, "-73.03238869",	"Cl. 1 #31-1 a 31-155"]];
        $ruta6 = [["5.844478166",	"-73.03691626","Cl. 16, Duitama"],
            ["5.843410859",	"-73.03359032","Cl. 20 #1-1 a 1-179"],
            ["5.839355071",	"-73.03249598","Cl. 20, Duitama"],
            ["5.835896955",	"-73.0329895","Av. Circunvalar, Duitama"],
            ["5.833591532",	"-73.03571463","Av. Circunvalar"],
            ["5.832139965",	"-73.04122925","Cra. 7 #10-30 a 10-124"],
            ["5.828895272",	"-73.04322481","Cra. 9a #8-1 a 8-155"],
            ["5.826632514",	"-73.04127216","Av. Circunvalar, Duitama"],
            ["5.826419046",	"-73.03807497","Dg. 11a #13-101"],
            ["5.827742547",	"-73.03174496","Cl. 18 #14-54"],
            ["5.828767191",	"-73.02816153","Cl. 21 #17-1"],
            ["5.824882073",	"-73.02483559","Cra. 24 #22-1 a 22-89"],
            ["5.822320441",	"-73.02571535","Cl. 20 #26-1 a 26-51"],
            ["5.819118385",	"-73.02462101","Cl. 19a, Duitama"],
            ["5.815788228",	"-73.02545786","Cl. 17 #33-1 a 33-51"],
            ["5.814208531",	"-73.02024364","Cra. 37 #20a-2 a 20a-132"],
            ["5.812201883",	"-73.01919222","Cra. 40 #21-1 a 21-161"],
            ["5.812201883",	"-73.01919222","Cra. 40 #21-1 a 21-161"],
            ["5.810109837",	"-73.01831245","Cl. 21 #40-309 a 40-439"],
            ["5.807035797",	"-73.01846266","Cra. 44 #19-1 a 19-81"],
            ["5.805840333",	"-73.01196098","Rìo Chicamocha, Tibasosa"],
            ["5.797856273",	"-73.00011635","Duitama-Tibasosa"]];
        $ruta7 = [["5.822363135", "-73.02391291","Cra. 27 #21a-2 a 21a-60"],
            ["5.822363135", "-73.02391291","Cra. 27 #21a-2 a 21a-60"],
            ["5.822299094", "-73.02518964","Cra. 27 #20-2 a 20-88"],
            ["5.819950922", "-73.02561879","Cra. 30 #199a-2 a 199a-58"],
            ["5.82076211" , "-73.03082228","Cra. 25 #14-1 a 14-47"],
            ["5.818115071", "-73.03132653","Cra. 27 #12-1 a 12-97"],
            ["5.818477972", "-73.02848339","Cl. 16 #28a-2 a 28a-264"],
            ["5.818179112", "-73.02559733","Cl. 19 #30-2 a 30-226"],
            ["5.8160017"  , "-73.026402","Cra. 32 #16-2 a 16-110"],
            ["5.815339936", "-73.02810788","Carrera 31c #14a-2 a 14a-26"],
            ["5.812564788", "-73.02835464","Cl. 13 #33-2 a 33-64"],
            ["5.810067142", "-73.02697062","Cl. 13 #37a-1 a 37a-107"],
            ["5.808807642", "-73.02474976","Cra. 39 #14-2 a 14-54"],
            ["5.809768278", "-73.0312407","Cl. 6b #35a-1 a 35a-19"],
            ["5.809768278", "-73.0312407","Cl. 6b #35a-1 a 35a-19"],
            ["5.806630194", "-73.03457737","Cra. 35 #1-1 a 1-179"],
            ["5.80387635" , "-73.03104758","Cra. 42 #1-2 a 1-280"],
            ["5.801101145", "-73.02762508",""],
            ["5.801101145", "-73.02762508",""],
            ["5.812820956", "-73.03252816","Cra. 30 #7-12 a 7-156"],
            ["5.812820956", "-73.03252816","Cra. 30 #7-12 a 7-156"],
            ["5.812820956", "-73.03252816","Cra. 30 #7-12 a 7-156"]];

$rutas = [$ruta1,$ruta2,$ruta3,$ruta4,$ruta5,$ruta6,$ruta7];



 $data = "fin";
 foreach ($dias as $dia){
     foreach ($identificaciones as $identificacion){
         $fecha = Carbon::parse('2018-03-'.$dia.' 08:00:00');
         $randoMinutes = random_int(0,30);
         $fecha->addMinutes($randoMinutes);

         $randoRuta = random_int(0,6);
         foreach ( $rutas[$randoRuta] as $ruta){
            $geo = new GeoPosicion();
             $geo->latitud = $ruta[0];
             $geo->longitud = $ruta[1];
             $geo->direccion = $ruta[2];
             $geo->fecha = $fecha;
             $geo->identificacion = $identificacion;
             $geo->save();
             $fecha->addMinutes(27);
         }
     }
 }

        return $data;
    }

    public function insertlocoMedallo(){

//       $dias = [1,2,4,5,8,9,11,15,17,18,19,22,23,25,26,30,31];
       //$dias = [1,2,5,6,7,9,12,14,15,16,19,21,22,23,26,28,];
         $dias = [2,5,4,8,9,11,15,16,18,19,22];

        $identificaciones = ["1030578176","52987428","72289819","16270094","52878399","53006165"];


        $ruta1 = [["6.244203","-75.58121189999997","Cl. 42 #63b-1 a 63b-61"],
            ["6.243669740448088","-75.58441982198178","Cl. 38 #66a-02"],
            ["6.243691070840605","-75.5868552677673	","Cra. 66b #1-2 a 1-54"],
            ["6.244373642941948","-75.59293851781308","Tv. 74 #3-2 a 3-80"],
            ["6.242176610814138","-75.60251936841428","Cra. 81 #34A-96"],
            ["6.240263122516343","-75.60821056365967","Cra. 84 #33aa-3"],
            ["6.236359620687356","-75.60964822769165","Cl. 32a #87-88"],
            ["6.235549053821590","-75.61644535545355","Parqueadero Altos Del Castillo"],
            ["6.227152059223104","-75.60838160287307","Cra. 84bc #23-2"],
            ["6.224782731396994","-75.60258311083544","Cra. 81 #19-1 a 19-147"],
            ["6.224526757402016","-75.6005768184923	","Cl. 19a #80-53 a 80-203"],
            ["6.223054904510525","-75.59854906847704","Cl. 18a #76a-50"],
            ["6.223588185020012","-75.59478324702013","Cl. 20 #72-1 a 72-107"],
            ["6.221327071942442","-75.59348505785692","Cra. 71 #15"],
            ["6.218553995454086","-75.59587758829821","Cl. 10 #72-103"],
            ["6.215204952815089","-75.59687922172623","Cl. 7 #70-500"],
            ["6.212645160626696","-75.59669683151321","Dg. 75b #1-147 a 1-241"],
            ["6.209296080381927","-75.60018370323257","Cl. 2b Sur #75da-28 a 75da-102"],
            ["6.207568202626648","-75.60324142150955","Cra. 79aa #280"],
            ["6.204791456504386","-75.60563395195084","Cl. 8 Sur #83a-22"],
            ["6.203212888245745","-75.60279081039505","Cl. 9 Sur #79c-222 a 79c-366"],
            ["6.202423602343659","-75.59900353126602","Cl. 9b Sur #74a-133 a 74a-499"]];
        $ruta2 = [["6.155739735","-75.59565805","Cl. 48c Sur #39a-50"],
            ["6.157275777","-75.58694624","Cra. 36D #43 Sur-100"],
            ["6.160091843","-75.57763361","Cra. 27b #37b-2 a 37b-100"],
            ["6.177414585","-75.58153891","Tv 27A Sur #31d-1 a 31d-209"],
            ["6.194315148","-75.59507847","Cl. 15b Sur #55-2 a 55-248"],
            ["6.190901954","-75.60688019","Vía El Pedregal, Itagüi"],
            ["6.197216346","-75.56855679","Cra. 35 #4a Sur-53 a 4a Sur-89"],
            ["6.204383942","-75.55061817","Cra. 15"],
            ["6.199093583","-75.5355978","Cl. 8"],
            ["6.194827125","-75.50409794","Cra. 34 Este"],
            ["6.172945977","-75.50713592","Vía Vda. Perico"],
            ["6.15104504" ,"-75.52336886","Unnamed Road"],
            ["6.176575984","-75.57147172","Cra. 27c #23 Sur-273 a 23 Sur-361"],
            ["6.192940038","-75.57412295","Cl. 12 Sur"],
            ["6.193978221","-75.57784705","Cl. 12 Sur #43a-85 a 43a-243"],
            ["6.194699131","-75.5795002","Av. Las Vegas"],
            ["6.19824385" ,"-75.58737278","Cl. 10b Sur #51-32"],
            ["6.198972702","-75.58979512","Cl. 10b Sur #51a-32"],
            ["6.198848266","-75.59397698","Cl. 12 Sur #54-32,"],
            ["6.194930237","-75.59478761","Cl. 13c Sur #53-87 a 53-283"],
            ["6.192971212","-75.59532167","Cl. 84a #54-2 a 54-118"],
            ["6.189006918","-75.59847356","Cra. 55 #76-83 a 76-183"]];
        $ruta3 = [["6.259167569","-75,56117536","	Cl. 63 #50-63"],
            ["6.259914111","-75,56730749","	Cl. 61 #52-2 a 52-126"],
            ["6.258101082","-75,56880477","	Cra. 53 #59-100"],
            ["6.259956773","-75,56008819","	Av. Ecuador #63a-1 a 63a-197"],
            ["6.262239051","-75,55188659","	Cl. 67 #40-1"],
            ["6.253237869","-75,55771355","	Cra. 41 #59-59"],
            ["6.249206485","-75,5706263","Av. Ayacucho #52-1 a 52-111"],
            ["6.238263996","-75,56748872","	Cl. 39a #43a-2 a 43a-152"],
            ["6.233464587","-75,57190423","	Cra. 45 #32-73 a 32-305"],
            ["6.228323838","-75,56945329","	Av. El Poblado"],
            ["6.223268364","-75,56914812","	Av. El Poblado #23-2 a 23-118"],
            ["6.213007947","-75,56729799","	Cl. 12 #39-36 a 39-352"],
            ["6.204368564","-75,56364543","	Cl. 5g #32-2 a 32-152"],
            ["6.195302392","-75,56136615","	Cl. 4 Sur #29-2 a 29-96"],
            ["6.195302392","-75,56136615","	Cl. 4 Sur #29-2 a 29-96"],
            ["6.188284002","-75,56372173","	Cl. 11 Sur #25-43 a 25-209"],
            ["6.179644214","-75,5694247","Cl. 20b Sur #27-1"],
            ["6.176209593","-75,57916172","	Tv. 32a Sur #29-1 a 29-123"],
            ["6.174993605","-75,58872708","	Cra. 45b #32c Sur-34 a 32c Sur-70"],
            ["6.173350949","-75,60352811","	Cl. 54A,"],
            ["6.173350949","-75,60352811","	Cl. 54A,"],
            ["6.173350949","-75,60352811","	Cl. 54A,"]];
        $ruta4 = [["5.770435","-73.062948","Calle Pantano de Vargas, Paipa, Boyacá"],
            ["5.76806","-73.059907","Calle Pantano de Vargas, Paipa, Boyacá"],
            ["5.771271","-73.054934",""],
            ["5.773858","-73.050781","Vía Tibasosa, Tibasosa, Boyacá"],
            ["5.779622","-73.053231",""],
            ["5.793277","-73.044592",""],
            ["5.806554","-73.036264","Cra. 35 #3o-10 a 3o-58, Duitama, Boyacá"],
            ["5.815875","-73.030113","Cl. 12, Duitama, Boyacá"],
            ["5.817756","-73.027717","Cl. 16 #30-2 a 30-136, Duitama"],
            ["5.815312","-73.026252","Cra. 33 #15a-40 a 15a-94, Duitama, Boyacá"],
            ["5.815995","-73.028832","Cl. 14 #30a-2 a 30a-72, Duitama, Boyacá"],
            ["5.820925","-73.032436","Cl. 12a #22-30 a 22-82, Duitama, Boyacá"],
            ["5.825642","-73.03507","Cl. 13 #16-2 a 16-102, Duitama, Boyacá"],
            ["5.829389","-73.033453","Cl. 17 #14-2 a 14-108, Duitama, Boyacá"],
            ["5.829656","-73.028259","Cra. 17 #21-2 a 21-130, Duitama, Boyacá"],
            ["5.833379","-73.026479","Cra. 16a #26-2 a 26-192, Duitama, Boyacá"],
            ["5.83382","-73.027358","Cl. 27 #10-1 a 10-15, Duitama, Boyacá"],
            ["5.832136","-73.029965","Cl. 23 #13-2 a 13-18, Duitama, Boyacá"],
            ["5.83274","-73.038171","Cra. 8a #13-2 a 13-104, Duitama, Boyacá"],
            ["5.834981","-73.035322","Cl 17A #7b-2 a 7b-18, Duitama, Boyacá"],
            ["5.840175","-73.035895","Cl. 17 #3-2 a 3-140, Duitama, Boyacá"],
            ["5.844051","-73.035909","Cl. 18 #1e-2 a 1e-78, Duitama, Boyacá"]];
        $ruta5 = [["6.24978925" ,"-75.58235239","Cra. 64 #44B-10"],
            ["6.24978925" ,"-75.58235239","Cra. 64 #44B-10"],
            ["6.252380856","-75.59434484","Cra. 76 #45C-86"],
            ["6.252284872","-75.60290407","Cra. 82 #44b-2 a 44b-114"],
            ["6.250183859","-75.61184954","Cra. 91a #38-1 a 38-157"],
            ["6.253671324","-75.6227262","Cl. 40"],
            ["6.253671324","-75.6227262","Cl. 40"],
            ["6.253671324","-75.6227262","Cl. 40"],
            ["6.260827486","-75.6124885","Cl. 48cc #97a-21 a 97a-103"],
            ["6.262437876","-75.60401034","Cra. 86 #49d-18 a 49d-152"],
            ["6.263280396","-75.59493136","Cl. 51 #78a-3 a 78a-77"],
            ["6.263280396","-75.59493136","Cl. 51 #78a-3 a 78a-77"],
            ["6.263280396","-75.59493136","Cl. 51 #78a-3 a 78a-77"],
            ["6.271972133","-75.5995853","Cl. 57a #83a-1 a 83a-61"],
            ["6.271705518","-75.61063362","Cl. 55 #102-1 a 102-39"],
            ["6.270927001","-75.62206817","Cl. 53 #110-2"],
            ["6.280076416","-75.63770535","Cra. 133"],
            ["6.280076416","-75.63770535","Cra. 133"],
            ["6.281249506","-75.6170583","Cl. 63B #77"],
            ["6.28455547" ,"-75.60482267","Cra. 97 #64d-195"],
            ["6.28455547" ,"-75.60482267","Cra. 97 #64d-195"],
            ["6.28455547" ,"-75.60482267","Cra. 97 #64d-195"]];
        $ruta6 = [["6.35361328 ","-75.58633218",""],
            ["6.35361328 ","-75.58633218",""],
            ["6.343632678","-75.57858836","Cl. 57 #68c-65"],
            ["6.316249002","-75.58114422","Cl. 25c #75a-80 a 75a-120"],
            ["6.296627415","-75.57286636","Cra. 71a #99-2 a 99-154"],
            ["6.259173716","-75.58808704","Cl. 50 #71-18"],
            ["6.259173716","-75.58808704","Cl. 50 #71-18"],
            ["6.258064573","-75.56008718","Cra. 47 #60-56"],
            ["6.248615387","-75.556468","Cl. 56 #37-90"],
            ["6.248530066","-75.55286311","Cl. 57c #34-75"],
            ["6.247762179","-75.54844283","Cl. 57b #29-62"],
            ["6.2478475 " ,"-75.54591082","Cl. 57aa #24b-133"],
            ["6.247676858","-75.54363631","Cra. 23"],
            ["6.244946581","-75.54402255","Cra. 25 #56c-103 a 56c-149"],
            ["6.241960325","-75.54269217","Cl. 56b #21-1 a 21-57"],
            ["6.239485986","-75.54316424","Cra. 19 #56-1 a 56-113"],
            ["6.233513394","-75.54440879","Cra. 16aa #46e-1 a 46e-43"],
            ["6.233513394","-75.54440879","Cra. 16aa #46e-1 a 46e-43"],
            ["6.228052679","-75.54736995","Cra. 19 #35-2 a 35-88"],
            ["6.230527073","-75.56020163","Cra. 32a #32a-1 a 32a-47"],
            ["6.227284762","-75.56393527","Cl. 29 #37a-185"],
            ["6.229299115","-75.57057381","Cl. 30 #43a-1 a 43a-87"]];
        $ruta7 = [["6.221449256","-75.59576511","Cra. 73 #14-1 a 14-39"],
            ["6.221449256","-75.59576511","Cra. 73 #14-1 a 14-39"],
            ["6.210698173","-75.60039997","Cl. 1 Sur #77a-1 a 77a-17"],
            ["6.201312127","-75.60396194","Cra. 82 #9A Sur-79"],
            ["6.197472333","-75.59937","Cl. 15b Sur #55-404"],
            ["6.195168443","-75.59803963","Cra. 58 Sur 40 #14"],
            ["6.195168443","-75.59803963","Cra. 58 Sur 40 #14"],
            ["6.195168443","-75.59803963","Cra. 58 Sur 40 #14"],
            ["6.183563511","-75.59027195","Cra. 42 #75-219, Itagüi"],
            ["6.181259561","-75.59752464","Cl. 70 #49-2 a 49-52"],
            ["6.182454203","-75.60194492","Cra 53B #66b-1 a 66b-61"],
            ["6.181600887","-75.60559273","Cra. 58 #63a-2 a 63a-36"],
            ["6.179296928","-75.6071806	","Cl. 60 #56-1 a 56-117"],
            ["6.171702325","-75.60679436","Cl. 53 #48-2 a 48-78"],
            ["6.171702325","-75.60679436","Cl. 53 #48-2 a 48-78"],
            ["6.171702325","-75.60679436","Cl. 53 #48-2 a 48-78"],
            ["6.16291293 ","-75.62932491","Parqueadero Iguazú"],
            ["6.16291293 ","-75.62932491","Parqueadero Iguazú"],
            ["6.165131626","-75.63958168","Cl. 27a"],
            ["6.161206234","-75.64125538","Cra. 57, La Estrella"],
            ["6.161206234","-75.64125538","Cra. 57, La Estrella"],
            ["6.161206234","-75.64125538","Cra. 57, La Estrella"]];

        $rutas = [$ruta1,$ruta2,$ruta3,$ruta4,$ruta5,$ruta6,$ruta7];



        $data = "fin";
        foreach ($dias as $dia){
            foreach ($identificaciones as $identificacion){
                $fecha = Carbon::parse('2018-03-'.$dia.' 08:00:00');
                $randoMinutes = random_int(0,30);
                $fecha->addMinutes($randoMinutes);

                $randoRuta = random_int(0,6);
                foreach ( $rutas[$randoRuta] as $ruta){
                    $geo = new GeoPosicion();
                    $geo->latitud = $ruta[0];
                    $geo->longitud = $ruta[1];
                    $geo->direccion = $ruta[2];
                    $geo->fecha = $fecha;
                    $geo->identificacion = $identificacion;
                    $geo->save();
                    $fecha->addMinutes(27);
                }
            }
        }


        return $data;
    }

    public function insertlocoBogota(){

//       $dias = [1,2,4,5,8,9,11,15,17,18,19,22,23,25,26,30,31];
//        $dias = [1,2,5,6,7,9,12,14,15,16,19,21,22,23,26,28,];
        $dias = [2,5,4,8,9,11,15,16,18,19,22];

        $identificaciones = ["101024617","52240980","1016037061"];

        $ruta1 = [["4.677431464290485","-74.07842953865429","Cra. 62 #77-1 a 77-63"],
            ["4.6750370651217","-74.0835870128096","Cra. 65 #70-1 a 70-71"],
            ["4.677768297003726","-74.08322801058193","Cl. 74a #65b-50 a 65b-98"],
            ["4.675773113536953","-74.08872402138184","Cra 68B #68a-73"],
            ["4.675773113536953","-74.08872402138184","Cra 68B #68a-73"],
            ["4.674749912229419","-74.09213751554489","Cl. 66 #68g-1 a 68g-55"],
            ["4.674049510848169","-74.09632020411408","Cra. 69j #64D-24"],
            ["4.671597964499944","-74.09937221378755","Cra. 69n #63a-3"],
            ["4.671543428925148","-74.10378069366953","Cl. 55 #71-24"],
            ["4.669421305125015","-74.10402914202166","Cra. 70c #53-1 a 53-55"],
            ["4.666401951239042","-74.10576106274073","Cra. 70 #51-17 a 51-81"],
            ["4.666401951239042","-74.10576106274073","Cra. 70 #51-17 a 51-81"],
            ["4.661848513502290","-74.1096818447113	","Ac. 26 #69D-91"],
            ["4.660608084720132","-74.11633372306824","Av. Boyacá #23c-26 a 23c-82"],
            ["4.654784459501301","-74.1138768196106","Dg. 23a #69-2 a 69-98"],
            ["4.656436882907751","-74.11017537117004","Cra. 69a #24a-1 a 24a-99"],
            ["4.656436882907751","-74.11017537117004","Cra. 69a #24a-1 a 24a-99"],
            ["4.656436882907751","-74.11017537117004","Cra. 69a #24a-1 a 24a-99"],
            ["4.656436882907751","-74.11017537117004","Cra. 69a #24a-1 a 24a-99"],
            ["4.65417065192551","-74.1047465801239","Ak 68, Bogotá"],
            ["4.655154449455341","-74.09980058670044","Cra. 67 #45-3 a 45-97"],
            ["4.653999556562542","-74.09719347953796","Cl. 53 #66-1 a 66-99"]];
        $ruta2 = [["4.64787236370981","-74,08855789364770","Cl. 46 #53-2 a 53-98"],
            ["4.64847120250834","-74,08669107617340","Cl. 56b #51-2 a 51-56"],
            ["4.64924113735867","-74,08164852322540","Cra. 38 #58a-3 a 58a-5"],
            ["4.64697410455798","-74,07905214489890","Ak 30 #5-7"],
            ["4.64671745887577","-74,07315128506620","Tv. 24a #60-1 a 60-99"],
            ["4.64962610446832","-74,06705730618430","Cra. 16a #61a-2 a 61a-98"],
            ["4.65021058482196","-74,05477601777940","Cra. 4 #69A-22"],
            ["4.66081847576266","-74,05741531145000","Cl. 75 #11-2 a 11-98"],
            ["4.66962974732395","-74,06155664216900","Cl. 82 #21a-1 a 21a-65"],
            ["4.66962974732395","-74,06155664216900","Cl. 82 #21a-1 a 21a-65"],
            ["4.66962974732395","-74,06155664216900","Cl. 82 #21a-1 a 21a-65"],
            ["4.67647337703410","-74,05205089342030","Ak. 15 #92-88"],
            ["4.67743146429048","-74.07842953865429","Cra. 62 #77-1 a 77-63"],
            ["4.6750370651217","-74.08358701280969","Cra. 65 #70-1 a 70-71"],
            ["4.67776829700372","-74.08322801058193","Cl. 74a #65b-50 a 65b-98"],
            ["4.67577311353695","-74.08872402138184","Cra 68B #68a-73"],
            ["4.67577311353695","-74.08872402138184","Cra 68B #68a-73"],
            ["4.67474991222941","-74.09213751554489","Cl. 66 #68g-1 a 68g-55"],
            ["4.67404951084816","-74.09632020411408","Cra. 69j #64D-24"],
            ["4.67159796449994","-74.09937221378755","Cra. 69n #63a-3"],
            ["4.67154342892514","-74.10378069366953","Cl. 55 #71-24"],
            ["4.66942130512501","-74.10402914202166","Cra. 70c #53-1 a 53-55"]];
        $ruta3 = [["4.695806269","-74.07099802","Cra. 70 #108-78"],
            ["4.695806269","-74.07831508","Cl. 98a #70d-29"],
            ["4.703932768","-74.07219965","Cl. 118 #70-14 a 70-42"],
            ["4.705729139","-74.0626939","Cl. 123a #53b-2 a 53b-60"],
            ["4.714069372","-74.05687887","Cl. 128b #49-99"],
            ["4.720570408","-74.06956035","Cra 58C #130-2 a 130-98"],
            ["4.723649825","-74.08172685","Cl. 129c #82-1 a 82-59"],
            ["4.723649825","-74.08172685","Cl. 129c #82-1 a 82-59"],
            ["4.723649825","-74.08172685","Cl. 129c #82-1 a 82-59"],
            ["4.728055078","-74.09329254","Cl. 130 #95-1 a 95-43"],
            ["4.728696617","-74.09816343","Cl. 129 #100a-1 a 100a-73"],
            ["4.739474389","-74.09702617","Cra. 103c #136-1 a 136-87"],
            ["4.74417892 ","-74.10490114","Cl. 139 Bis #114-1 a 114-85"],
            ["4.749353866","-74.11633808","Cra. 143 #142a-99"],
            ["4.746659477","-74.12155229","Cra. 150b #138-1 a 138-99"],
            ["4.743708468","-74.12354786","Cl. 135 #152-2 a 152-98"],
            ["4.743708468","-74.12354786","Cl. 135 #152-2 a 152-98"],
            ["4.749375091","-74.11081742","Cl. 143 #129-2 a 129-40"],
            ["4.751214112","-74.1022558","Cra. 114a #147a-2 a 147a-86"],
            ["4.751214112","-74.1022558","Cra. 114a #147a-2 a 147a-86"],
            ["4.751214112","-74.1022558","Cra. 114a #147a-2 a 147a-86"],
            ["4.751214112","-74.1022558","Cra. 114a #147a-2 a 147a-86"]];
//        $ruta4 = [["5.770435","-73.062948","Calle Pantano de Vargas, Paipa, Boyacá"],
//            ["5.76806","-73.059907","Calle Pantano de Vargas, Paipa, Boyacá"],
//            ["5.771271","-73.054934",""],
//            ["5.773858","-73.050781","Vía Tibasosa, Tibasosa, Boyacá"],
//            ["5.779622","-73.053231",""],
//            ["5.793277","-73.044592",""],
//            ["5.806554","-73.036264","Cra. 35 #3o-10 a 3o-58, Duitama, Boyacá"],
//            ["5.815875","-73.030113","Cl. 12, Duitama, Boyacá"],
//            ["5.817756","-73.027717","Cl. 16 #30-2 a 30-136, Duitama"],
//            ["5.815312","-73.026252","Cra. 33 #15a-40 a 15a-94, Duitama, Boyacá"],
//            ["5.815995","-73.028832","Cl. 14 #30a-2 a 30a-72, Duitama, Boyacá"],
//            ["5.820925","-73.032436","Cl. 12a #22-30 a 22-82, Duitama, Boyacá"],
//            ["5.825642","-73.03507","Cl. 13 #16-2 a 16-102, Duitama, Boyacá"],
//            ["5.829389","-73.033453","Cl. 17 #14-2 a 14-108, Duitama, Boyacá"],
//            ["5.829656","-73.028259","Cra. 17 #21-2 a 21-130, Duitama, Boyacá"],
//            ["5.833379","-73.026479","Cra. 16a #26-2 a 26-192, Duitama, Boyacá"],
//            ["5.83382","-73.027358","Cl. 27 #10-1 a 10-15, Duitama, Boyacá"],
//            ["5.832136","-73.029965","Cl. 23 #13-2 a 13-18, Duitama, Boyacá"],
//            ["5.83274","-73.038171","Cra. 8a #13-2 a 13-104, Duitama, Boyacá"],
//            ["5.834981","-73.035322","Cl 17A #7b-2 a 7b-18, Duitama, Boyacá"],
//            ["5.840175","-73.035895","Cl. 17 #3-2 a 3-140, Duitama, Boyacá"],
//            ["5.844051","-73.035909","Cl. 18 #1e-2 a 1e-78, Duitama, Boyacá"]];
//        $ruta5 = [["6.24978925" ,"-75.58235239","Cra. 64 #44B-10"],
//            ["6.24978925" ,"-75.58235239","Cra. 64 #44B-10"],
//            ["6.252380856","-75.59434484","Cra. 76 #45C-86"],
//            ["6.252284872","-75.60290407","Cra. 82 #44b-2 a 44b-114"],
//            ["6.250183859","-75.61184954","Cra. 91a #38-1 a 38-157"],
//            ["6.253671324","-75.6227262","Cl. 40"],
//            ["6.253671324","-75.6227262","Cl. 40"],
//            ["6.253671324","-75.6227262","Cl. 40"],
//            ["6.260827486","-75.6124885","Cl. 48cc #97a-21 a 97a-103"],
//            ["6.262437876","-75.60401034","Cra. 86 #49d-18 a 49d-152"],
//            ["6.263280396","-75.59493136","Cl. 51 #78a-3 a 78a-77"],
//            ["6.263280396","-75.59493136","Cl. 51 #78a-3 a 78a-77"],
//            ["6.263280396","-75.59493136","Cl. 51 #78a-3 a 78a-77"],
//            ["6.271972133","-75.5995853","Cl. 57a #83a-1 a 83a-61"],
//            ["6.271705518","-75.61063362","Cl. 55 #102-1 a 102-39"],
//            ["6.270927001","-75.62206817","Cl. 53 #110-2"],
//            ["6.280076416","-75.63770535","Cra. 133"],
//            ["6.280076416","-75.63770535","Cra. 133"],
//            ["6.281249506","-75.6170583","Cl. 63B #77"],
//            ["6.28455547" ,"-75.60482267","Cra. 97 #64d-195"],
//            ["6.28455547" ,"-75.60482267","Cra. 97 #64d-195"],
//            ["6.28455547" ,"-75.60482267","Cra. 97 #64d-195"]];
//        $ruta6 = [["6.35361328 ","-75.58633218",""],
//            ["6.35361328 ","-75.58633218",""],
//            ["6.343632678","-75.57858836","Cl. 57 #68c-65"],
//            ["6.316249002","-75.58114422","Cl. 25c #75a-80 a 75a-120"],
//            ["6.296627415","-75.57286636","Cra. 71a #99-2 a 99-154"],
//            ["6.259173716","-75.58808704","Cl. 50 #71-18"],
//            ["6.259173716","-75.58808704","Cl. 50 #71-18"],
//            ["6.258064573","-75.56008718","Cra. 47 #60-56"],
//            ["6.248615387","-75.556468","Cl. 56 #37-90"],
//            ["6.248530066","-75.55286311","Cl. 57c #34-75"],
//            ["6.247762179","-75.54844283","Cl. 57b #29-62"],
//            ["6.2478475 " ,"-75.54591082","Cl. 57aa #24b-133"],
//            ["6.247676858","-75.54363631","Cra. 23"],
//            ["6.244946581","-75.54402255","Cra. 25 #56c-103 a 56c-149"],
//            ["6.241960325","-75.54269217","Cl. 56b #21-1 a 21-57"],
//            ["6.239485986","-75.54316424","Cra. 19 #56-1 a 56-113"],
//            ["6.233513394","-75.54440879","Cra. 16aa #46e-1 a 46e-43"],
//            ["6.233513394","-75.54440879","Cra. 16aa #46e-1 a 46e-43"],
//            ["6.228052679","-75.54736995","Cra. 19 #35-2 a 35-88"],
//            ["6.230527073","-75.56020163","Cra. 32a #32a-1 a 32a-47"],
//            ["6.227284762","-75.56393527","Cl. 29 #37a-185"],
//            ["6.229299115","-75.57057381","Cl. 30 #43a-1 a 43a-87"]];
//        $ruta7 = [["6.221449256","-75.59576511","Cra. 73 #14-1 a 14-39"],
//            ["6.221449256","-75.59576511","Cra. 73 #14-1 a 14-39"],
//            ["6.210698173","-75.60039997","Cl. 1 Sur #77a-1 a 77a-17"],
//            ["6.201312127","-75.60396194","Cra. 82 #9A Sur-79"],
//            ["6.197472333","-75.59937","Cl. 15b Sur #55-404"],
//            ["6.195168443","-75.59803963","Cra. 58 Sur 40 #14"],
//            ["6.195168443","-75.59803963","Cra. 58 Sur 40 #14"],
//            ["6.195168443","-75.59803963","Cra. 58 Sur 40 #14"],
//            ["6.183563511","-75.59027195","Cra. 42 #75-219, Itagüi"],
//            ["6.181259561","-75.59752464","Cl. 70 #49-2 a 49-52"],
//            ["6.182454203","-75.60194492","Cra 53B #66b-1 a 66b-61"],
//            ["6.181600887","-75.60559273","Cra. 58 #63a-2 a 63a-36"],
//            ["6.179296928","-75.6071806	","Cl. 60 #56-1 a 56-117"],
//            ["6.171702325","-75.60679436","Cl. 53 #48-2 a 48-78"],
//            ["6.171702325","-75.60679436","Cl. 53 #48-2 a 48-78"],
//            ["6.171702325","-75.60679436","Cl. 53 #48-2 a 48-78"],
//            ["6.16291293 ","-75.62932491","Parqueadero Iguazú"],
//            ["6.16291293 ","-75.62932491","Parqueadero Iguazú"],
//            ["6.165131626","-75.63958168","Cl. 27a"],
//            ["6.161206234","-75.64125538","Cra. 57, La Estrella"],
//            ["6.161206234","-75.64125538","Cra. 57, La Estrella"],
//            ["6.161206234","-75.64125538","Cra. 57, La Estrella"]];

        $rutas = [$ruta1,$ruta2,$ruta3];



        $data = "fin";
        foreach ($dias as $dia){
            foreach ($identificaciones as $identificacion){
                $fecha = Carbon::parse('2018-03-'.$dia.' 08:00:00');
                $randoMinutes = random_int(0,30);
                $fecha->addMinutes($randoMinutes);

                $randoRuta = random_int(0,6);
                foreach ( $rutas[$randoRuta] as $ruta){
                    $geo = new GeoPosicion();
                    $geo->latitud = $ruta[0];
                    $geo->longitud = $ruta[1];
                    $geo->direccion = $ruta[2];
                    $geo->fecha = $fecha;
                    $geo->identificacion = $identificacion;
                    $geo->save();
                    $fecha->addMinutes(27);
                }
            }
        }


        return $data;
    }
    
}
