<?php

namespace LifeOnScreen\LaravelQuickBooks;

class QuickBooksTokenHandler implements QuickBooksTokenHandlerInterface
{
    /**
     * @var string
     */
    protected $realmIdKey = 'qb-realm-id';

    /**
     * @var string
     */
    protected $accessTokenKey = 'qb-access-token';

    /**
     * @var string
     */
    protected $refreshTokenKey = 'qb-refresh-token';

    /**
     * Store a token value
     *
     * @param string $key
     * @param string $value
     */
    public function set($key, $value)
    {
        return cache([$key => $value], 60 * 24 * 7); // 7 days
    }

    /**
     * Set the realm ID
     *
     * @param string $value
     */
    public function setRealmId($value)
    {
        $this->set($this->realmIdKey, $value);
    }

    /**
     * Set the access token
     *
     * @param $value
     */
    public function setAccessToken($value)
    {
        $this->set($this->accessTokenKey, $value);
    }

    /**
     * Set the refresh token
     *
     * @param $value
     */
    public function setRefreshToken($value)
    {
        $this->set($this->refreshTokenKey, $value);
    }

    /**
     * Retrieve a token value
     *
     * @param $key
     * @return string
     */
    public function get($key)
    {
        return cache($key);
    }

    /**
     * Get the realm ID
     */
    public function getRealmId()
    {
        return $this->get($this->realmIdKey);
    }

    /**
     * Get the access token
     */
    public function getAccessToken()
    {
        return $this->get($this->accessTokenKey);
    }

    /**
     * Get the refresh token
     */
    public function getRefreshToken()
    {
        return $this->get($this->refreshTokenKey);
    }


}
