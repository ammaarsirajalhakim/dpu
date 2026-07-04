<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

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
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        Route::middleware('auth')->get('/storage/{path}', function (string $path) {
            $disk = Storage::disk('public');

            abort_unless($disk->exists($path), 404);

            return $disk->response($path);
        })->where('path', '.*')->name('storage.show');
    }
}