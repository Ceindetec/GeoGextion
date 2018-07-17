<?php

namespace App\Http\Controllers;

use App\Asesor;
use App\Asesores;
use App\Empresas;
use App\GeoPosicion;
use App\User;
use App\UserAsesor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Yajra\DataTables\DataTables;
use PHPExcel_Worksheet_Drawing;
use Caffeinated\Shinobi\Facades\Shinobi;

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
            $asesores = Asesor::with('rol')->whereHas('rol', function ($query) {
                $query->where('slug', 'asesor');
            })->where('empresa_id', auth()->user()->empresa_id)
                ->where('estado', 'A')
                ->select([\DB::raw('concat(nombres," ",apellidos) as nombre, identificacion', 'estado')])
                ->pluck('nombre','identificacion');

        } else {
            $id =Auth::User()->id;
            $asesores = Asesor::with('rol')->whereHas('rol', function ($query){
                $query->where('roles.slug','asesor');
            })
                ->whereHas('supervisor', function ($query) use ($id){
                    $query->where('supervisor_id','=',$id);
                })
                ->select([\DB::raw('concat(nombres," ",apellidos) as nombre, identificacion', 'estado')])
                ->where('empresa_id',Auth::user()->empresa_id)
                ->pluck('nombre','identificacion');
        }

        return view('home', compact('asesores'));
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
            $asesores = Asesor::with('rol')->whereHas('rol', function ($query) {
                $query->where('slug', 'asesor');
            })->where('empresa_id', auth()->user()->empresa_id)
                ->where('estado', 'A')
                ->select([\DB::raw('concat(nombres," ",apellidos) as nombre, identificacion', 'estado')])
                ->pluck('nombre','identificacion');
        } else {
            $id =Auth::User()->id;
            $asesores = Asesor::with('rol')->whereHas('rol', function ($query){
                $query->where('roles.slug','asesor');
            })
                ->whereHas('supervisor', function ($query) use ($id){
                    $query->where('supervisor_id','=',$id);
                })
                ->select([\DB::raw('concat(nombres," ",apellidos) as nombre, identificacion', 'estado')])
                ->where('empresa_id',Auth::user()->empresa_id)
                ->pluck('nombre','identificacion');
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

    
}
