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
            $response->headers->set('Access-Control-Allow-Origin', $origin);
            $response->headers->set('Access-Control-Allow-Headers', 'Origin, Content-Type, Cookie, Accept, Authorization');
            $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PATCH, PUT, OPTIONS, DELETE');
            $response->headers->set('Access-Control-Allow-Credentials', 'true');
        }

        return $response;
    }
}
