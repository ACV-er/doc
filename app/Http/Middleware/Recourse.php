<?php

namespace App\Http\Middleware;

use Closure;

class Recourse
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
        // TODO 处理已过期求助，及时放还积分，检测是否有被采纳的回答
        return $next($request);
    }
}
