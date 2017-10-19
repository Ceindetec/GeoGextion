<?php

namespace App\Http\Controllers;

use App\Asesores;
use App\GeoPosicion;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Yajra\DataTables\DataTables;
use PHPExcel_Worksheet_Drawing;

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
        $asesores = Asesores::select([\DB::raw('concat(nombres," ",apellidos) as nombre, identificacion','estado')])
            ->where('estado','A')
            ->pluck('nombre','identificacion')
            ->all();
        $asesores = array_add($asesores,'','Seleccione');
        arsort($asesores);
        return view('home', compact('asesores'));
    }

    public function listaAsesores(){
        return view('asesores.listaasesores');
    }

    public function gridAsesores(){
        $asesores = Asesores::all();
        return DataTables::of($asesores)
            ->addColumn('action',function ($asesores){
                $acciones = '<div class="btn-group">';
                $acciones .= '<a class="btn btn-xs btn-success" data-modal href="'.route('asesor.editar', $asesores->id).'">Editar</a>';
                if ($asesores->estado == 'A')
                    $acciones .= '<button class="btn btn-xs btn-danger" onclick="cambiarestado('.$asesores->id.')">Inactivar</button>';
                else
                    $acciones .= '<button class="btn btn-xs btn-success" onclick="cambiarestado('.$asesores->id.')">Activar</button>';
                $acciones .='</div>';
                return $acciones;
            })
            ->make(true);
    }

    public function viewCrearAsesor(){
        return view('asesores.modalcrearasesor');
    }

    public function crearAsesor(Request $request){
        $result=[];
        try{
            $validator = \Validator::make($request->all(), [
                'identificacion' => 'required|unique:asesores|max:11',
                'email' => 'unique:asesores',
            ]);

            if ($validator->fails()) {
                return $validator->errors()->all();
            }

            $asesor = new Asesores($request->all());
            $asesor->save();
            $result['estado']=true;
            $result['mensaje']='Asesor agregado satisfactoriamente.';
        }catch (\Exception $exception){
            $result['estado']=false;
            $result['mensaje']='Asesor agregado satisfactoriamente. '.$exception->getMessage();
        }
        return $result;

    }

    public function viewEditarAsesor($id){
        $asesor = Asesores::find($id);
        return view('asesores.modaleditarasesor',compact('asesor'));
    }

    public function editarAsesor(Request $request, $id){
        $result = [];
        try{
            $asesor = Asesores::find($id);
            if($asesor->identificacion  != $request->identificacion){
                if($asesor->email != $request->email){
                    $validator = \Validator::make($request->all(), [
                        'identificacion' => 'required|unique:asesores|max:11',
                        'email' => 'unique:asesores',
                    ]);

                    if ($validator->fails()) {
                        return $validator->errors()->all();
                    }
                }else{
                    $validator = \Validator::make($request->all(), [
                        'identificacion' => 'required|unique:asesores|max:11',
                    ]);

                    if ($validator->fails()) {
                        return $validator->errors()->all();
                    }
                }
            }else if($asesor->email != $request->email){
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
        }catch (\Exception $exception){
            $result['estado'] = false;
            $result['mensaje'] = 'No fue posible actualizar al asesor. '.$exception->getMessage();
        }
        return $result;
    }

    public function cambiarEstadoAsesor(Request $request){
        $result = [];
        try{
            $asesor = Asesores::find($request->id);
            if($asesor->estado == 'A'){
                $asesor->estado='I';
            }else{
                $asesor->estado = 'A';
            }
            $asesor->save();
            $result['estado']= true;
            $result['mensaje'] = 'Se cambiado el estado satisfactoriamente';
        }catch (\Exception $exception){
            $result['estado']= false;
            $result['mensaje'] = 'No fue posible cambiar el estado '.$exception->getMessage();
        }
        return $result;
    }

    public function geoPosicionfinal(){
        $markets = Asesores::where('estado','A')->get();
        foreach ($markets as $market){
            $market->getPosition;
        }
        return $markets;
    }

    public function ubicarasesor(Request $request){
        $markets = Asesores::where('estado','A')
            ->where('identificacion',$request->identificacion)
            ->first();
        $markets->getPosition;
        return $markets;
    }

    public function rutaasesor(Request $request){
        $markets = Asesores::where('estado','A')
            ->where('identificacion',$request->identificacion)->first();
        $markets = $markets->getRuta($request->fecha)->get();

        return $markets;
    }

    public function updatemarketgeneral(){
        $response = new StreamedResponse(function() {
            $old_fecha = GeoPosicion::whereDate('fecha',Carbon::now()->format('Y-m-d'))
                ->orderBy('fecha','desc')->first();

            while (true) {
                $new_fecha = GeoPosicion::whereDate('fecha',Carbon::now()->format('Y-m-d'))
                    ->orderBy('fecha','desc')->first();

                if ($new_fecha->fecha > $old_fecha->fecha) {
                    $markets = Asesores::where('estado','A')->get();
                    foreach ($markets as $market){
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

    public function consulta(){
        $asesores = Asesores::select([\DB::raw('concat(nombres," ",apellidos) as nombre, identificacion','estado')])
            ->where('estado','A')
            ->pluck('nombre','identificacion')
            ->all();
        return view('consulta.consulta',compact('asesores'));
    }

    public function resultadoConsulta(Request $request)
    {
        $geposiciones = GeoPosicion::where('identificacion',$request->asesor)
            ->whereDate('fecha',$request->fecha)
        ->whereBetween('fecha',[$request->fecha." ".$request->hora1, $request->fecha." ".$request->hora2])
        ->get();
        return view('consulta.resultado', compact('geposiciones', 'request'));
    }

    public function exportarPdf(Request $request)
    {
        $geposiciones = GeoPosicion::where('identificacion',$request->asesor)
            ->whereDate('fecha',$request->fecha)
            ->whereBetween('fecha',[$request->fecha." ".$request->hora1, $request->fecha." ".$request->hora2])
            ->get();
        $data=['geposiciones'=>$geposiciones];
        $pdf = \PDF::loadView('consulta.exportarpdfconsulta', $data);
        $pdf->setPaper('A4', 'landscape');
        return $pdf->download('geoposiciones - '.Carbon::now()->format('d-m-Y').'.pdf');
    }

    public function exportarExcel(Request $request)
    {
        $geposiciones = GeoPosicion::where('identificacion',$request->asesor)
            ->whereDate('fecha',$request->fecha)
            ->whereBetween('fecha',[$request->fecha." ".$request->hora1, $request->fecha." ".$request->hora2])
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
                $sheet->row(4, array('', 'NOMBRE DEL ASESOR:', $geposiciones[0]->getAsesor->nombres.' '.$geposiciones[0]->getAsesor->apellidos, ''));
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
                                $miresul->latitud.', '.$miresul->longitud,
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

    public function modalPunto(Request $request){
        $geposicion = GeoPosicion::find($request->id);
        return view('consulta.modalmapapunto',compact('geposicion'));
    }
}
