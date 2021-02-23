<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use User;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $permission)
    {
    	$redirect = false;
    	if (!Auth::check()) {
    	    $redirect = true;
    	}else{
    		$user = Auth::user();
    		if(!$user->ican($permission)){
    			$redirect = true;
    		}
    	}
    	if($redirect){
    		if($request->ajax()){
    			$data = ["success" => 0, "message" => "Unauthorize Access", "redirect_url" => route("dashboard")];
    			return response()->json($data);
    		}else{
    			return redirect(route("private.dashboard"));
    		}
    	}
        return $next($request);
    }
}
