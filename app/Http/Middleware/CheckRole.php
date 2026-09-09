<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    // public function handle(Request $request, Closure $next): Response
    // {
    //     return $next($request);
    // }

    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
         if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'You need to be logged in.');
        }

        $user_type = Auth::user()->user_type;
        
        if (in_array($user_type, $roles)) {
            return $next($request);
        }else{
            return redirect()->route('dashboard')->with('error', 'You do not have access this page');
        }
    }
}



