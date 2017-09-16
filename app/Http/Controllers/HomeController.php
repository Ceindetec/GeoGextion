<?php

namespace App\Http\Controllers;

use App\Asesores;
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
        return view('home');
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
                $acciones .= '<button class="btn btn-xs btn-danger">Inactivar</button>';
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
        dd($id);
    }
}
