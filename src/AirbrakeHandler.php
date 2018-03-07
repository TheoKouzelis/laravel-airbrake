<?php

namespace Kouz\LaravelAirbrake;

use Airbrake\Notifier;
use Illuminate\Contracts\Foundation\Application;

class AirbrakeHandler
{
    protected $app;

    /**
     * The application implementation.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Build airbrake notifier.
     *
     * @return Airbrake\Notifier
     */
    public function handle()
    {
        $options = collect(config('airbrake'))
            ->filter()
            ->toArray();

        return new Notifier($options);
    }
}
