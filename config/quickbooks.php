<?php

return [

    'data-service' => [
        /**
         * OAuth protocol used by your app.
         */
        'auth-mode'     => 'oauth2',

        /**
         * Client ID from the app's keys tab.
         */
        'client-id'     => env('QB_CLIENT_ID'),

        /**
         * Client Secret from the app's keys tab.
         */
        'client-secret' => env('QB_CLIENT_SECRET'),

        /**
         * The redirect URI provided on the Redirect URIs part under keys tab.
         */
        'redirect-uri'  => env('QB_REDIRECT_URI'),

        /**
         * com.intuit.quickbooks.accounting or com.intuit.quickbooks.payment
         */
        'scope'         => env('QB_SCOPE', 'com.intuit.quickbooks.accounting'),

        /**
         * Development/Production
         */
        'base-url'      => env('QB_BASE_URL', 'https://sandbox-quickbooks.api.intuit.com'),
    ],
];