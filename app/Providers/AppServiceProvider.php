<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Schema;
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
        Blade::component('components.frontend.form', 'form-component');
        Blade::component('components.backend.notes.notes', 'backend-notes-component');
        Blade::component('components.backend.documents.documents', 'backend-documents-component');
        Schema::defaultStringLength(191);
        Paginator::useBootstrapFive();
        //Paginator::useBootstrap(); // Enables Bootstrap 4 styling
    }
}
