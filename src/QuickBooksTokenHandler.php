<?php

namespace LifeOnScreen\LaravelQuickBooks;

class QuickBooksTokenHandler implements QuickBooksTokenHandlerInterface
{
    public function set($key, $value)
    {
        return cache([$key => $value], 60 * 24 * 7); // 7 days
    }

    public function get($key)
    {
        return cache($key);
    }
}
