<?php

namespace App\Http\Controllers;

use App\Http\Actions\ActionInterface;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use ReflectionMethod;


class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function callAction($method, $parameters)
    {
        $attributes = $this->getAttributes($method);
        $actions = [];

        foreach ($attributes as $attribute) {
            if(array_key_exists($attribute->name, config('app.attributes'))) {
                $action = app(config('app.attributes')[$attribute->name]);
                $resultAction = $action->before($attribute->getArguments());
                if ($resultAction !== null) {
                    return $resultAction;
                }
                $actions[] = $action;
            }
        }

        $result = $this->{$method}(...array_values($parameters));

        foreach ($actions as $action) {
            $resultAction = $action->after(array_merge($attribute->getArguments(), ['response' => $result]));
            if ($resultAction !== null) {
                return $resultAction;
            }
        }

        return $result;
    }

    private function getAttributes(string $method): array
    {
        $reflectionMethod = new ReflectionMethod(static::class, $method);

        return $reflectionMethod->getAttributes();
    }
}
