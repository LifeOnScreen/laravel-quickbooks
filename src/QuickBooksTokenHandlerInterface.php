<?php

namespace LifeOnScreen\LaravelQuickBooks;

interface QuickBooksTokenHandlerInterface
{
    public function get($key);

    public function getRealmId();

    public function getAccessToken();

    public function getRefreshToken();

    public function set($key, $value);

    public function setRealmId($value);

    public function setAccessToken($value);

    public function setRefreshToken($value);
}