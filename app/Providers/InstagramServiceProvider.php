<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Helpers\Instagram2Helper;
use GuzzleHttp\Client;

class InstagramServiceProvider extends ServiceProvider
{
    protected $defer = true;

   /**
     * Register bindings in the container.
     *
     * @return  InstagramMockService
     */
    public function register()
    {
        $this->app->when(Instagram2Helper::class)
            ->needs(Client::class)
            ->give(function ($app) {

            $data = [
                'request.options' => [
                    'timeout'         => 60,
                    'connect_timeout' => 30
                ]
            ];

            $instagramHttpService = getenv('INSTAGRAM_HTTP_SERVICE');

            return new $instagramHttpService($data);

        });
    }
}
