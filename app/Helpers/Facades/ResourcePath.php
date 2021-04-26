<?php

namespace App\Helpers\Facades;

use Illuminate\Support\Facades\Facade;

class ResourcePath extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'resourcePath';
    }
}