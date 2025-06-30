<?php

namespace Laravelcrudgenerator\LaravelCrudGenerator;

use Illuminate\Support\ServiceProvider;
use Laravelcrudgenerator\LaravelCrudGenerator\Commands\MakeCrudCommand;

class LaravelCrudGeneratorServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
   public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeCrudCommand::class,
            ]);
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'laravel-crud-generator');

        // Register the main class to use with the facade
        $this->app->singleton('laravel-crud-generator', function () {
            return new LaravelCrudGenerator;
        });
    }
}
