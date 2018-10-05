<?php

namespace LifeOnScreen\LaravelQuickBooks;

interface QuickBooksTokenHandlerInterface
{
    public function set($key, $value);

    public function get($key);
}