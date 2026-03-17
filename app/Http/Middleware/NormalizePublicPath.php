<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class NormalizePublicPath
{
    public function handle(Request $request, Closure $next): Response
    {
        $path = '/' . ltrim($request->path(), '/');

        if ($path === '/public' || str_starts_with($path, '/public/')) {
            $target = substr($path, strlen('/public'));
            $target = $target === '' ? '/' : $target;

            if ($query = $request->getQueryString()) {
                $target .= '?' . $query;
            }

            return response('', 301, [
                'Location' => $target,
            ]);
        }

        return $next($request);
    }
}
