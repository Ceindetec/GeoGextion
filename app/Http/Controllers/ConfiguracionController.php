<?php

namespace App\Http\Controllers;

use App\Empresas;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class ConfiguracionController extends Controller
{
    //

    public function index()
    {
        try{

            $empresa = Empresas::findOrfail(auth()->user()->empresa_id);
            return view('configuracion.configempresa', compact('empresa'));
        }catch (ModelNotFoundException $exception){
            return abort(404);
        }catch (\Exception $exception){
            return abort(404);
        }
    }


    public function guardar(Request $request)
    {
        try{
            $empresa = Empresas::query()->findOrFail(auth()->user()->empresa_id);
            $empresa->color = $request->color;
            if($request->logo != null){

                if(file_exists($empresa->logo))
                    unlink($empresa->logo);

                $imagen = time() . '.' . $request->logo->getClientOriginalExtension();
                $img = \Image::make($request->file('logo'));
                $img->save('images/logo/' . utf8_decode($imagen));
                $empresa->logo = 'images/logo/' . utf8_decode($imagen);
            }
            $empresa->save();
            return redirect()->back();
        }catch (\Exception $exception){
            return abort(404);
        }
    }
}
