<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class Administrador
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
        if(auth()->user()->isRole('superadmin')){
            return redirect('listaempresas');
        }else if(auth()->user()->isRole('sadminempresa')||auth()->user()->isRole('admin')) {
            //return redirect('listaempresas');
        }else{
            return redirect('home');
        }
        return $next($request);
    }
}
