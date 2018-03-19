<?php

namespace App\Http\Middleware;


use Closure;
use Auth;

class SuperAdministradorEmpresa
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(Auth::user()->isRole('superadmin')){
            return redirect('listaempresas');
        }else if(Auth::user()->isRole('sadminempresa')) {
            //return redirect('listaempresas');
        }else{
            return redirect('home');
        }
        return $next($request);
    }
}
