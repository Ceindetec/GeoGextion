<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PerfilUsuarioController extends Controller
{
    //

    public function perfilUsuario(){
        return view('perfil.perfil');
    }

    public function actulizarPerfil(Request $request){
        try{

            $usuario = Auth::user();
            if($usuario->email != $request->email){
                $validator = \Validator::make($request->all(), [
                    'email' => 'required|unique:users',
                ]);

                if ($validator->fails()) {
                    return redirect()->back()->with('error', 'Email ya se encuentra en usu!');
                }
            }

            $usuario->email = strtolower($request->email);
            $usuario->name = strtoupper($request->name);
            if($request->password =! null){
                $usuario->password = bcrypt($request->input('password'));
            }
            if($usuario->save()){
                return redirect()->back()->with('status', 'Actulizado satisfactoriamente!');
            }else{
                return redirect()->back()->with('error', 'Ocurrio un error !');
            }

        }catch (\Exception $exception){
            return redirect()->back()->with('error', 'Ocurrio un error !'.$exception->getMessage());
        }

    }
}
