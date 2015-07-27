<?php

namespace Pingpong\Confirmation;

use Illuminate\Support\ServiceProvider;

class ConfirmationServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $configPath = config_path('confirmation.php');

        $this->publishes([
            __DIR__.'/../../config/config.php' => $configPath
        ], 'config');

        if (file_exists($configPath)) {
            $this->mergeConfigFrom($configPath, 'confirmation');
        }

        $this->publishes([
            __DIR__.'/../../migrations' => base_path('database/migrations')
        ], 'migration');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(Contracts\Confirmator::class, Confirmator::class);
    }
}
