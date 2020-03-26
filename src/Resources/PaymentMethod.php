<?php

namespace LifeOnScreen\LaravelQuickBooks\Resources;

use LifeOnScreen\LaravelQuickBooks\QuickBooksResource;
use LifeOnScreen\LaravelQuickBooks\Facades as QuickBooksFacades;

class PaymentMethod extends QuickBooksResource
{
    /**
     * The name of this resource.
     *
     * @var string
     */
    protected $name = 'PaymentMethod';

    /**
     * QuickBooks Online API Facade
     *
     * @var string
     */
    protected $facade = QuickBooksFacades\PaymentMethod::class;
}