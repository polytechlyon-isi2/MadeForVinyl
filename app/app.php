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
$app->register(new Silex\Provider\SessionServiceProvider());
$app->register(new Silex\Provider\SecurityServiceProvider(), array(
    'security.firewalls' => array(
        'secured' => array(
            'pattern' => '^/',
            'anonymous' => true,
            'logout' => true,
            'form' => array('login_path' => '/login', 'check_path' => '/login_check'),
            'users' => $app->share(function () use ($app) {
                return new MadeForVinyl\DAO\UserDAO($app['db']);
            }),
        ),
    ),
    'security.role_hierarchy' => array(
        'ROLE_ADMIN' => array('ROLE_USER'),
    ),
    'security.access_rules' => array(
        array('^/admin', 'ROLE_ADMIN'),
    ),
));
$app->register(new Silex\Provider\FormServiceProvider());
$app->register(new Silex\Provider\TranslationServiceProvider());
$app->register(new Silex\Provider\ValidatorServiceProvider());
$app['twig'] = $app->share($app->extend('twig', function(Twig_Environment $twig, $app) {
    $twig->addExtension(new Twig_Extensions_Extension_Text());
    return $twig;
}));

// Register services
//Categories
$app['dao.category'] = $app->share(function ($app) {
    return new MadeForVinyl\DAO\CategoryDAO($app['db']);
});
//Vinyls
$app['dao.vinyl'] = $app->share(function ($app) {
    $vinylDAO = new MadeForVinyl\DAO\VinylDAO($app['db']);
    $vinylDAO->setCategoryDAO($app['dao.category']);
    return $vinylDAO;
});
//Users
$app['dao.user'] = $app->share(function ($app) {
    return new MadeForVinyl\DAO\UserDAO($app['db']);
});
//Baskets
$app['dao.basket'] = $app->share(function ($app){
    $basketDAO = new MadeForVinyl\DAO\BasketDAO($app['db']);
    $basketDAO->setUserDAO($app['dao.user']);
    $basketDAO->setVinylDAO($app['dao.vinyl']);
    return $basketDAO;
});