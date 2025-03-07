<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use ReflectionClass;
use Symfony\Component\HttpFoundation\Response;

class CacheResponse
{
    public function handle(Request $request, Closure $next): Response
    {
        $route = $request->route();
        if ($route === null) {
            return $next($request);
        }

        $cacheKey = sprintf(
            'response-%s@%s:%s',
            $controller = $route->getControllerClass(),
            $method = $route->getActionMethod(),
            $request->attributes ? md5(json_encode($request->attributes)) : '',
        );

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        return $next($request);
    }
}
