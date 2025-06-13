<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator; // Asegúrate de importar Paginator al inicio del archivo
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
    Paginator::useBootstrapFive(); // O Paginator::useBootstrapFour(); si usas Bootstrap 4
    }
}
