<?php

namespace App\Providers;

use Illuminate\Foundation\Console\ServeCommand;
use Illuminate\Pagination\Paginator;
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
        Paginator::useBootstrapFive();

        if (config('app.env') === 'production' || env('FORCE_HTTPS', false)) {
            URL::forceScheme('https');
        }

        if (class_exists(ServeCommand::class)) {
            ServeCommand::$passthroughVariables[] = 'SystemRoot';
            ServeCommand::$passthroughVariables[] = 'SystemDrive';
            ServeCommand::$passthroughVariables[] = 'windir';
        }
    }
}
