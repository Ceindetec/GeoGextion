<?php

namespace App\Http\Middleware;

use Closure;

class noHome
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
        }else{
            //return redirect('home');
        }

        return $next($request);
    }
}
