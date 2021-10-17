<?php

namespace App\Http\Middleware;

use Auth;
use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::user()->isAdmin()) {
            session()->flash('error', __("You don\'t have permission to show thi page"));
            return redirect()->route('home');
        }
        return $next($request);
    }
}
