<?php

namespace App\Http\Actions;

interface ActionInterface
{
    public function after(array $params);
    public function before(array $params);
}