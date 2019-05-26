<?php

namespace App\Http\Middleware;

use Closure;

class RecoOwnerShip
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
        // TODO 验证求助的所有者
        return $next($request);
    }
}
