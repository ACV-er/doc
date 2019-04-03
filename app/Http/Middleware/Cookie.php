<?php

namespace App\Http\Middleware;

use Closure;

class Cookie
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
        $response = $next($request);
        $origin = $request->server('HTTP_ORIGIN') ?: '';
//        $allow_origin = [
//            'http://localhost',
//            'http://127.0.0.1',
//        ];
        if (env('APP_ENV') != 'production') {
            $response->header('Access-Control-Allow-Origin', $origin);
            $response->header('Access-Control-Allow-Headers', 'Origin, Content-Type, Cookie, Accept, Authorization');
            $response->header('Access-Control-Allow-Methods', 'GET, POST, PATCH, PUT, OPTIONS, DELETE');
            $response->header('Access-Control-Allow-Credentials', 'true');
        }

        return $response;
    }
}
