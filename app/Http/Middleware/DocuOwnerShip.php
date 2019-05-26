<?php

namespace App\Http\Middleware;

use App\Document;
use Closure;

class DocuOwnerShip
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
        $document = Document::query()->find($request->route('id'));
        if(!$document) {
            return response(msg(10, "目标不存在，或已删除" . __LINE__), 200);
        }

        if(!($document->uploader === session('id'))) {
            return response(msg(3, "你不是上传者,无权更改" . __LINE__), 200);
        }

        return $next($request);
    }
}
