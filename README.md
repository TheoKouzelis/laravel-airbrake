# Laravel Airbrake

This is a Laravel service provider for the latest Airbrake PHP package https://github.com/airbrake/phpbrake

The service provider will configure an instance of Airbrake\Notifier with an ID, key and environment name.

## Install
Require via composer.
```
composer require kouz/laravel-airbrake
```
For Laravel >=5.5 the package will be discoverd. For Laravel <=5.4 add package to list of service providers in config/app.php
```
<?php
  //config/app.php
  
  'providers' => [
    Kouz\LaravelAirbrake\ServiceProvider::class,
  ],
```
Publish and fill out the config/airbrake.php file with your ID and key.
```
php artisan vendor:publish --provider="Kouz\LaravelAirbrake\ServiceProvider"
```

## Config
The variables projectId and projectKey are required. Leave the rest empty to use Airbrake's default values.
```
    'projectId'     => '',
    'projectKey'    => '',
    'environment'   => env('APP_ENV', 'production'),

    //leave the following options empty to use defaults

    'appVersion'    => '',
    'host'          => '',
    'rootDirectory' => '',
    'httpClient'    => '',
```

## Basic Usage
### 5.6 Custom Channel
Add the custom "airbrake" channel (outlined below) to config/logging.php. Then add the "airbrake" channel to the stack channel.
```
//config/logging.php

    'channels' => [
        'stack' => [
            'driver' => 'stack',
            'channels' => ['single', 'airbrake'],
        ],

        'airbrake' => [
            'driver' => 'custom',
            'via' => Kouz\LaravelAirbrake\AirbrakeLogger::class,
            'level' => 'debug',
        ],
    ]
```

### Exception Handler
The easiest way to notify airbrake is through the laravel exception handler as shown in the following code snippet. Inject or make a new instance
of a Airbrake\Notifier object then pass a exception to the notify function.

```
//app/Exceptions/Handler.php

/**
 * Report or log an exception.
 *
 * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
 *
 * @param  \Exception  $exception
 * @return void
 */
public function report(Exception $exception)
{
    if ($this->shouldReport($exception)) {
        $airbrakeNotifier = \App::make('Airbrake\Notifier');
        $airbrakeNotifier->notify($exception);
    }

    parent::report($exception);
}
```

### Custom Monolog Configuration 
To configure it as a Monolog handler you will have to create a custom configuration in bootstrap/app.php. This callback function is called 
before the service providers are loaded. So it is necessary to directly use our AirbrakeHandler class instead of the provider.

```
//bootstrap/app.php

$app->configureMonologUsing(function($monolog) use ($app) {
    $airbrakeNotifier = (new Kouz\LaravelAirbrake\AirbrakeHandler($app))->handle();
    $monologHandler = new Airbrake\MonologHandler($airbrakeNotifier, Monolog\Logger::ERROR);
    $monolog->pushHandler($monologHandler);
});
```
