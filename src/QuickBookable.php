<?php

namespace LifeOnScreen\LaravelQuickBooks;

interface QuickBookable
{
    public function getQuickBooksIdAttribute();

    public function syncToQuickbooks();
}