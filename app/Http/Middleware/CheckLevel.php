<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class CheckLevel
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next,...$level)
    {
        if(in_array($request->user()->level,$level)){
            return $next($request);
        }else{
            return response()->json(['message'=>'You cant access this page','statusCode'=>411,'status'=>'need request']);
            
        }
        return response()->json(['message'=>'You cant access this page','statusCode'=>411,'status'=>'need request']);
      
    }
}
