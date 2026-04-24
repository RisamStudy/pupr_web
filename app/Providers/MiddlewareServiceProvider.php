<?php

namespace App\Providers;

use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\OperatorHelperMiddleware;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class MiddlewareServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Route::aliasMiddleware('admin', AdminMiddleware::class);
        Route::aliasMiddleware('operator_helper', OperatorHelperMiddleware::class);
    }
}
