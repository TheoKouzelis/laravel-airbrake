<?php

return [

    'projectId'     => '',
    'projectKey'    => '',
    'environment'   => env('APP_ENV', 'production'),

    //leave the following options empty to use defaults
    
    'host'          => null, #airbrake api host e.g.: 'api.airbrake.io' or 'http://errbit.example.com
    'appVersion'    => null,
    'revision'      => null, #git revision
    'rootDirectory' => null,
    'keysBlacklist' => null, #list of keys containing sensitive information that must be filtered out
    'httpClient'    => null, #http client implementing GuzzleHttp\ClientInterface

];
