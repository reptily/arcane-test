<?php

namespace App\Http\Actions;

use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Cache;

class CacheAction implements ActionInterface
{

    public function __construct(private Request $request)
    {
    }

    public function after(array $params): void
    {
        $route = $this->request->route();
        if ($route === null) {
            return;
        }

        $cacheKey = $this->getCacheTag($route);
        Cache::set(
            $cacheKey,
            serialize($params['response'] ?? []),
            $params['maxage'] ?? config('cache.default_maxage')
        );
    }

    public function before(array $params)
    {
        $route = $this->request->route();
        if ($route === null) {
            return null;
        }

        $cacheKey = $this->getCacheTag($route);
        if (Cache::has($cacheKey)) {
            return response(unserialize(Cache::get($cacheKey)));
        }
    }

    private function getCacheTag(Route $route)
    {
        return sprintf(
            'response-%s@%s:%s',
            $route->getControllerClass(),
            $route->getActionMethod(),
            $this->request->attributes ? md5(json_encode($this->request->attributes)) : '',
        );
    }
}