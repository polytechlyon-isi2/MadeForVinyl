<?php

use Symfony\Component\Debug\ErrorHandler;
use Symfony\Component\Debug\ExceptionHandler;
use MadeForVinyl\Controller\HomeController;


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

//Bar debogage Symfony
$app->register(new Silex\Provider\MonologServiceProvider(), array(
    'monolog.logfile' => __DIR__.'/../var/logs/madeforvinyl.log',
    'monolog.name' => 'MadeForVinyl',
    'monolog.level' => $app['monolog.level']
));
$app->register(new Silex\Provider\ServiceControllerServiceProvider());
if (isset($app['debug']) && $app['debug']) {
    $app->register(new Silex\Provider\HttpFragmentServiceProvider());
    $app->register(new Silex\Provider\WebProfilerServiceProvider(), array(
        'profiler.cache_dir' => __DIR__.'/../var/cache/profiler'
    ));
}

// Register error handler
$app->error(function (\Exception $e, $code) use ($app) {
    switch ($code) {
        case 403:
            $message = 'Access denied.';
            break;
        case 404:
            $message = 'The requested resource could not be found.';
            break;
        default:
            $message = "Something went wrong.";
    }
    return $app['twig']->render('error.html.twig', array(
        'message' => $message,
        'categories' => $app['dao.category']->findAll()
        )
    );
});


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