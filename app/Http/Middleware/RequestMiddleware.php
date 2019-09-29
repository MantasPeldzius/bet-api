<?php

namespace App\Http\Middleware;

use Closure;

class RequestMiddleware
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
        
        if (!$request->isJson()) {
            return response('Not JSON', 401);
        }
        
        return $next($request);
    }
}
