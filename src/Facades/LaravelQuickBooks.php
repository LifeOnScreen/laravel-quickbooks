<?php

namespace LifeOnScreen\LaravelQuickBooks\Facades;

use Illuminate\Support\Facades\Facade;

class LaravelQuickBooks extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'laravel-quickbooks';
    }
}
