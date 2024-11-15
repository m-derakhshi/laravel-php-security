<?php

namespace MDerakhshi\SecurityCheck;

use Illuminate\Support\ServiceProvider;

class LaravelPHPSecurityServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Load package views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'laravel-php-security-check');

        // Publish views if in console
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/laravel-php-security-check'),
            ], 'views');
        }
    }
}
