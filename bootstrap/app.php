<?php

require_once __DIR__.'/../vendor/autoload.php';

try {
    (new Dotenv\Dotenv(__DIR__.'/../'))->load();
} catch (Dotenv\Exception\InvalidPathException $e) {
    //
}

$app = new Laravel\Lumen\Application(
    realpath(__DIR__.'/../')
);

$app->withFacades();

$app->withEloquent();

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

$app->middleware([
    Clockwork\Support\Lumen\ClockworkMiddleware::class
]);

$app->register(Clockwork\Support\Lumen\ClockworkServiceProvider::class);
$app->register(App\Providers\FacebookServiceProvider::class);
$app->register(App\Providers\InstagramServiceProvider::class);
$app->register(App\Providers\TwitterServiceProvider::class);

$app->router->group(['namespace' => 'App\Http\Controllers'], function ($app) {
    require __DIR__.'/../app/Http/routes.php';
});

return $app;
