<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IfMe
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(!Auth::id() !== $request->route('id')){
            return response()->json([
                'response_code'=>403,
                'status'=>'error',
                'message'=>'You are not the owner of this profile',
            ], 403);
        }
        return $next($request);
    }
}
