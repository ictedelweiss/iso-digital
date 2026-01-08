<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use SocialiteProviders\Azure\AzureExtendSocialite;
use SocialiteProviders\Manager\SocialiteWasCalled;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Force Root URL to handle subdirectory deployment
        if (config('app.url')) {
            \Illuminate\Support\Facades\URL::forceRootUrl(config('app.url'));
        }

        // Configure Livewire for subdirectory (Apache Alias)
        \Livewire\Livewire::setScriptRoute(function ($handle) {
            return \Illuminate\Support\Facades\Route::get('/iso-digital/laravel-app/livewire/livewire.js', $handle);
        });

        \Livewire\Livewire::setUpdateRoute(function ($handle) {
            return \Illuminate\Support\Facades\Route::post('/iso-digital/laravel-app/livewire/update', $handle);
        });

        // Register Azure Socialite Provider
        Event::listen(function (SocialiteWasCalled $event) {
            $event->extendSocialite('azure', \SocialiteProviders\Azure\Provider::class);
        });

        // Register Microsoft Graph Mail Driver
        \Illuminate\Support\Facades\Mail::extend('microsoft-graph', function (array $config = []) {
            return new \App\Mail\Transport\MicrosoftGraphTransport(
                config('services.microsoft_graph.tenant_id'),
                config('services.microsoft_graph.client_id'),
                config('services.microsoft_graph.client_secret'),
                config('services.microsoft_graph.from_address')
            );
        });
    }
}
