<?php

namespace LifeOnScreen\LaravelQuickBooks\Controllers;

use Illuminate\Routing\Controller;
use LifeOnScreen\LaravelQuickBooks\QuickbooksConnect;
use Request;

/**
 * Class QuickbooksController
 * @package LifeOnScreen\LaravelQuickBooks\Controllers
 */
class QuickbooksController extends Controller
{
    /**
     * @throws \QuickBooksOnline\API\Exception\SdkException
     */
    public function connect()
    {
        $url = (new QuickbooksConnect())->getAuthorizationUrl();

        header('Location: ' . $url);
    }

    /**
     * @throws \QuickBooksOnline\API\Exception\SdkException
     */
    public function refreshTokens()
    {
        if ((new QuickbooksConnect())->processHook(Request::get('realmId'), Request::get('code'))) {
            return 'Tokens successfully refreshed.';
        }

        return 'There were some problems refreshing tokens.';
    }
}