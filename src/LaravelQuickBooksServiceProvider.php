<?php

namespace LifeOnScreen\LaravelQuickBooks;

use Illuminate\Support\ServiceProvider;

class LaravelQuickBooksServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {

            // Publishing the configuration file.
            $this->publishes([
                __DIR__ . '/../config/quickbooks.php' => config_path('quickbooks.php'),
            ], 'quickbooks.config');
        }
    }

    /**
     * Register laravel-quickbooks package.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/quickbooks.php', 'quickbooks');

        // Register the service the package provides.
        $this->app->singleton('QuickBooksConnection', function ($app) {
            return new QuickBooksConnection;
        });

        // Register the service the package provides.
        $this->app->singleton(QuickBooksTokenHandlerInterface::class, QuickBooksTokenHandler::class);
    }

    /**
     * Get provided services.
     *
     * @return array
     */
    public function provides()
    {
        return ['QuickBooksConnection'];
    }
}