<?php

namespace LifeOnScreen\LaravelQuickBooks;

use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Request;
use QuickBooksOnline\API\DataService\DataService;

/**
 * Class Init
 * @package LifeOnScreen\LaravelQuickBooks
 */
class QuickbooksConnect
{
    /**
     * @return string
     * @throws \QuickBooksOnline\API\Exception\SdkException
     */
    public static function getAuthorizationUrl(): string
    {

        $cookieLife  = 30;
        $cookieValue = str_random(32);
        $validUntil = Carbon::now()->addMinutes($cookieLife)->timestamp;
        Cookie::queue(Cookie::make('quickbooks_auth', $cookieValue, $cookieLife));
        cache(['qb-auth-cookie' => "{$cookieValue}|{$validUntil}"], $cookieLife);

        return self::getDataService()->getOAuth2LoginHelper()->getAuthorizationCodeURL();
    }

    /**
     * Set realm id, access token and refresh token.
     * @return bool
     */
    public static function processHook(): bool
    {
        $realmID = Request::get('realmId');
        $requestCode = Request::get('code');
        if (empty($realmID) || empty($requestCode) || !self::cookieIsValid()) {
            return false;
        }

        try {

            $OAuth2LoginHelper = self::getDataService()->getOAuth2LoginHelper();
            $accessTokenObj = $OAuth2LoginHelper->exchangeAuthorizationCodeForToken($requestCode, $realmID);

            $accessTokenValue = $accessTokenObj->getAccessToken();
            $refreshTokenValue = $accessTokenObj->getRefreshToken();

            $tokenHandler = static::getTokenHandler();
            $tokenHandler->set('qb-realm-id', $realmID);
            $tokenHandler->set('qb-access-token', $accessTokenValue);
            $tokenHandler->set('qb-refresh-token', $refreshTokenValue);

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Get QuickBooksOnline\API\DataService\DataService object.
     * @throws \QuickBooksOnline\API\Exception\SdkException
     */
    protected static function getDataService(): DataService
    {
        return DataService::Configure([
            'auth_mode'    => config('quickbooks.data-service.auth-mode'),
            'ClientID'     => config('quickbooks.data-service.client-id'),
            'ClientSecret' => config('quickbooks.data-service.client-secret'),
            'RedirectURI'  => config('quickbooks.data-service.redirect-uri'),
            'scope'        => config('quickbooks.data-service.scope'),
            'baseUrl'      => config('quickbooks.data-service.base-url')
        ]);
    }

    protected static function getTokenHandler(): QuickBooksTokenHandlerInterface
    {
        return App::make(QuickBooksTokenHandlerInterface::class);
    }

    /**
     * Checks if the cookie is valid.
     * @return bool
     */
    protected static function cookieIsValid(): bool
    {
        $validCookie = explode('|', cache('qb-auth-cookie'));

        if ($validCookie[0] === Cookie::get('quickbooks_auth') && (int)$validCookie[1] > time()) {
            return true;
        }

        return false;
    }
}
