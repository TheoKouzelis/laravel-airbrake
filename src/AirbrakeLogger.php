<?php

namespace Kouz\LaravelAirbrake;

use Airbrake\MonologHandler;
use Airbrake\Notifier;
use Illuminate\Foundation\Application;
use Monolog\Logger as Monolog;

class AirbrakeLogger
{
    protected $airbrake;
    protected $app;
    protected $levels = [
        'debug'     => Monolog::DEBUG,
        'info'      => Monolog::INFO,
        'notice'    => Monolog::NOTICE,
        'warning'   => Monolog::WARNING,
        'error'     => Monolog::ERROR,
        'critical'  => Monolog::CRITICAL,
        'alert'     => Monolog::ALERT,
        'emergency' => Monolog::EMERGENCY,
    ];

    public function __construct(Application $app, Notifier $airbrake)
    {
        $this->airbrake = $airbrake;
        $this->app = $app;
    }

    public function __invoke(array $config)
    {
        return new Monolog($this->parseChannel($config), [
            new MonologHandler($this->airbrake, $this->level($config))
        ]);
    }

    protected function level(array $config)
    {
        $level = $config['level'] ?? 'debug';

        if (isset($this->levels[$level])) {
            return $this->levels[$level];
        }

        throw new InvalidArgumentException('Invalid log level.');
    }

    protected function parseChannel(array $config)
    {
        if (! isset($config['name'])) {
            return $this->app->bound('env') ? $this->app->environment() : 'production';
        }

        return $config['name'];
    }
}
