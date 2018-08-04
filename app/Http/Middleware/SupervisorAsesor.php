<?php

namespace App\Http\Middleware;

use Closure;

class SupervisorAsesor
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
        if(auth()->user()->isRole('superadmin')) {
            return redirect('listaempresas');
        }else if(auth()->user()->isRole('asesor') || auth()->user()->isRole('trasporte') || auth()->user()->isRole('supert')){
            return redirect('home');
        }
        return $next($request);
    }
}
