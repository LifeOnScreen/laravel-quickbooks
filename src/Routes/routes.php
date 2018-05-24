<?php

Route::get(
    '/quickbooks/connect',
    'LifeOnScreen\LaravelQuickBooks\Controllers\QuickbooksController@connect'
);

Route::get(
    '/quickbooks/refreshTokens',
    'LifeOnScreen\LaravelQuickBooks\Controllers\QuickbooksController@refreshTokens'
);