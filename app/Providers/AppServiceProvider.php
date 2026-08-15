<?php

namespace App\Providers;

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
        if (isset($_SERVER['HTTP_HOST'])) {
            $proto = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'];
            
            // Get the subdirectory path from SCRIPT_NAME if it exists
            $scriptDir = dirname($_SERVER['SCRIPT_NAME']);
            $basePath = ($scriptDir === '/' || $scriptDir === '\\') ? '' : $scriptDir;
            
            // Dynamically set app.url config
            config(['app.url' => $proto . '://' . $host . $basePath]);
        }
    }
}

