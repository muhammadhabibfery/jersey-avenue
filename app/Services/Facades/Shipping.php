<?php

namespace App\Services\Facades;

use Illuminate\Support\Facades\Facade;

class Shipping extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'Shipping';
    }
}
