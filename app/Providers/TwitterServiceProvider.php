<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Helpers\Twitter2Helper;
use Abraham\TwitterOAuth\TwitterOAuth;

class TwitterServiceProvider extends ServiceProvider
{
    protected $defer = true;

   /**
     * Register bindings in the container.
     *
     * @return  TwitterMockService
     */
    public function register()
    {
        $this->app->when(Twitter2Helper::class)
            ->needs(TwitterOAuth::class)
            ->give(function ($app) {

            $api_key    = getenv('TWITTER_API_KEY');
            $api_secret = getenv('TWITTER_API_SECRET');

            $twitterHttpService = getenv('TWITTER_HTTP_SERVICE');

            return new $twitterHttpService($api_key, $api_secret);

        });
    }
}
