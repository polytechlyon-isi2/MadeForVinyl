<?php

use Symfony\Component\Debug\ErrorHandler;
use Symfony\Component\Debug\ExceptionHandler;

// Register global error and exception handlers
ErrorHandler::register();
ExceptionHandler::register();

// Register service providers.
$app->register(new Silex\Provider\DoctrineServiceProvider());
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../views',
));
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());

// Register services
$app['dao.vinyl'] = $app->share(function ($app) {
    return new MadeForVinyl\DAO\VinylDAO($app['db']);
});
$app['dao.category'] = $app->share(function ($app) {
    return new MadeForVinyl\DAO\CategoryDAO($app['db']);
});