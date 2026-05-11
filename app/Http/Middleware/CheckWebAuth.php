<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckWebAuth
{
    public function handle(Request $request, Closure $next)
    {
        // We store a flag in session after login to protect blade routes
        if (!$request->session()->get('logged_in')) {
            return redirect()->route('login');
        }

        return $next($request);
    }
}