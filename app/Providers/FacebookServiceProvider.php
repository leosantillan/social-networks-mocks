<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Helpers\Facebook2Helper;
use GuzzleHttp\Client;

class FacebookServiceProvider extends ServiceProvider
{
    protected $defer = true;

   /**
     * Register bindings in the container.
     *
     * @return  FacebookMockService
     */
    public function register()
    {
        $this->app->when(Facebook2Helper::class)
            ->needs(Client::class)
            ->give(function ($app) {

            $data = [
                'request.options' => [
                    'timeout'         => 60,
                    'connect_timeout' => 30
                ]
            ];

            $facebookHttpService = getenv('FACEBOOK_HTTP_SERVICE');

            return new $facebookHttpService($data);

        });
    }
}
