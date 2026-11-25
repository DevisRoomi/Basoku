<?php

namespace App\Providers;

use App\Http\Middleware\EnsureAdmin;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

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
        Livewire::addPersistentMiddleware([
            EnsureAdmin::class,
        ]);

        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
