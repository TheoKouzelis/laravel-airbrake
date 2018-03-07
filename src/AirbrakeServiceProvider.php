<?php

namespace Kouz\LaravelAirbrake;

use Airbrake\Notifier;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/airbrake.php' => config_path('airbrake.php'),
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('Airbrake\Notifier', function ($app) {
            $handler = new AirbrakeHandler($app);

            return $handler->handle();
        });
    }
}
