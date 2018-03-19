<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use Caffeinated\Shinobi\Facades\Shinobi;

class SuperAdmin
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

        if(Auth::user()->isRole('superadmin')) {
            //return redirect('listaempresas');
        }else{
            return redirect('home');
        }


        return $next($request);
    }
}
