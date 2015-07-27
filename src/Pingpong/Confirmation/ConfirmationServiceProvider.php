<?php

namespace Pingpong\Confirmation;

use Illuminate\Support\ServiceProvider;

class ConfirmationServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $configPath = config_path('confirmation.php');

        $this->publishes([
            __DIR__.'/../../config/config.php' => $configPath,
        ], 'config');

        if (file_exists($configPath)) {
            $this->mergeConfigFrom($configPath, 'confirmation');
        }

        $this->publishes([
            __DIR__.'/../../migrations' => base_path('database/migrations'),
        ], 'migration');

        $viewsPath = base_path('resources/views/vendor/pingpong/confirmation');
        
        $viewSourcePath = __DIR__.'/../../views';

        $this->publishes([$viewSourcePath, $viewsPath]);

        $this->loadViewsFrom([$viewsPath, $viewSourcePath], 'confirmation');
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->app->bind(Contracts\Confirmator::class, Confirmator::class);
    }
}
