<?php

namespace LifeOnScreen\LaravelQuickBooks;

use Exception;
use QuickBooksOnline\API\DataService\DataService;

/**
 * Class Init
 * @package LifeOnScreen\LaravelQuickBooks
 */
class QuickbooksConnect
{
    /**
     * @var DataService
     */
    private $dataService;

    /**
     * Init constructor.
     * @throws \QuickBooksOnline\API\Exception\SdkException
     */
    public function __construct()
    {
        $this->dataService = DataService::Configure([
            'auth_mode'    => config('quickbooks.data-service.auth-mode'),
            'ClientID'     => config('quickbooks.data-service.client-id'),
            'ClientSecret' => config('quickbooks.data-service.client-secret'),
            'RedirectURI'  => config('quickbooks.data-service.redirect-uri'),
            'scope'        => config('quickbooks.data-service.scope'),
            'baseUrl'      => config('quickbooks.data-service.base-url')
        ]);
    }

    /**
     * @throws \QuickBooksOnline\API\Exception\SdkException
     */
    public function getAuthorizationUrl()
    {
        $OAuth2LoginHelper = $this->dataService->getOAuth2LoginHelper();

        return $OAuth2LoginHelper->getAuthorizationCodeURL();
    }

    /**
     * @param $requestCode
     * @param $realmID
     * @return bool
     */
    public function processHook(string $realmID, string $requestCode): bool
    {
        try {
            $OAuth2LoginHelper = $this->dataService->getOAuth2LoginHelper();
            $accessTokenObj = $OAuth2LoginHelper->exchangeAuthorizationCodeForToken($requestCode, $realmID);

            $accessTokenValue = $accessTokenObj->getAccessToken();
            $refreshTokenValue = $accessTokenObj->getRefreshToken();
            option(['qb-realm-id' => $realmID]);
            option(['qb-access-token' => $accessTokenValue]);
            option(['qb-refresh-token' => $refreshTokenValue]);
            return true;
        } catch (Exception $e) {
            return false;
        }

    }
}