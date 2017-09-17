<?php

namespace App\Http\Controllers;

use App\Asesores;
use App\GeoPosicion;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

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
                'email' => 'required|unique:asesores',
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
                        'email' => 'required|unique:asesores',
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
                    'email' => 'required|unique:asesores',
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
}
