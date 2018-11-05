<?php

namespace LifeOnScreen\LaravelQuickBooks;

use Illuminate\Support\Facades\App;
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
     *
     * @throws \QuickBooksOnline\API\Exception\SdkException
     * @throws \QuickBooksOnline\API\Exception\ServiceException
     */
    public function __construct()
    {
        $tokenHandler = $this->getTokenHandler();

        $this->dataService = DataService::Configure([
            'auth_mode'       => config('quickbooks.data-service.auth-mode'),
            'ClientID'        => config('quickbooks.data-service.client-id'),
            'ClientSecret'    => config('quickbooks.data-service.client-secret'),
            'accessTokenKey'  => $tokenHandler->getAccessToken(),
            'refreshTokenKey' => $tokenHandler->getRefreshToken(),
            'QBORealmID'      => $tokenHandler->getRealmId(),
            'baseUrl'         => config('quickbooks.data-service.base-url')
        ]);

        $oAuth2LoginHelper = $this->dataService->getOAuth2LoginHelper();
        $accessToken = $oAuth2LoginHelper->refreshToken();
        $this->dataService->updateOAuth2Token($accessToken);

        $tokenHandler->setAccessToken($accessToken->getAccessToken());
        $tokenHandler->setRefreshToken($accessToken->getRefreshToken());
    }

    /**
     * @return null|DataService
     */
    public function getDataService(): DataService
    {
        return $this->dataService;
    }

    /**
     * @return QuickBooksTokenHandlerInterface
     */
    private function getTokenHandler(): QuickBooksTokenHandlerInterface
    {
        return App::make(QuickBooksTokenHandlerInterface::class);
    }
}