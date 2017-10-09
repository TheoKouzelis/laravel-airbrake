<?php

namespace Kouz\Providers;

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
        $airbrake = new Notifier([
                'projectId'  => config('airbrake.id'),
                'projectKey' => config('airbrake.key'),
                'host' => config('airbrake.host'),
            ]);

        $airbrake->addFilter(function ($notice) {
            $this->setEnvName($notice);

            foreach ($this->getEnvKeys() as $envKey) {
                $this->filterEnvKey($notice, $envKey);
            }

            return $notice;
        });

        return $airbrake;
    }

    protected function filterEnvKey(&$notice, $envKey)
    {
        if (isset($notice['environment'][$envKey])) {
            $notice['environment'][$envKey] = 'FILTERED';
        }
    }

    protected function getEnvFile()
    {
        $filePath = $this->app->environmentPath() . '/' . $this->app->environmentFile();

        $envFile = @file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        return is_array($envFile) ? $envFile : [];
    }

    protected function getEnvKeyFromLine($envLine)
    {
        return trim(current(explode('=', $envLine)));
    }

    protected function getEnvKeys()
    {
        $envFile = $this->getEnvFile();

        $envKeys = array_map([$this, 'getEnvKeyFromLine'], $envFile);

        return $envKeys;
    }

    protected function setEnvName(&$notice)
    {
        $notice['context']['environment'] = env('APP_ENV');
    }
}
