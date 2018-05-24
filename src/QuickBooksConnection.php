<?php

namespace LifeOnScreen\LaravelQuickBooks;

use QuickBooksOnline\API\DataService\DataService;

/**
 * Class QuickBooksConnection
 * @package LifeOnScreen\LaravelQuickBooks
 */
class QuickBooksConnection
{
    /**
     * @var null|DataService
     */
    private $dataService = null;

    /**
     * QuickBooksConnection constructor.
     * @throws \QuickBooksOnline\API\Exception\SdkException
     * @throws \QuickBooksOnline\API\Exception\ServiceException
     */
    public function __construct()
    {
        $this->dataService = DataService::Configure([
            'auth_mode'       => config('quickbooks.data-service.auth-mode'),
            'ClientID'        => config('quickbooks.data-service.client-id'),
            'ClientSecret'    => config('quickbooks.data-service.client-secret'),
            'accessTokenKey'  => option('qb-access-token'),
            'refreshTokenKey' => option('qb-refresh-token'),
            'QBORealmID'      => option('qb-realm-id'),
            'baseUrl'         => config('quickbooks.data-service.base-url')
        ]);

        $oAuth2LoginHelper = $this->dataService->getOAuth2LoginHelper();
        $accessToken = $oAuth2LoginHelper->refreshToken();
        $this->dataService->updateOAuth2Token($accessToken);
        option(['qb-access-token' => $accessToken->getAccessToken()]);
        option(['qb-refresh-token' => $accessToken->getRefreshToken()]);
    }

    /**
     * @return null|DataService
     */
    public function getDataService()
    {
        return $this->dataService;
    }
}