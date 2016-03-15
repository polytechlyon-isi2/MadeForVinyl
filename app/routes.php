<?php
use Symfony\Component\HttpFoundation\Request;
use MadeForVinyl\Domain\User;
use MadeForVinyl\Form\Type\InscriptionType;
use MadeForVinyl\Form\Type\ProfilType;

// Home page
$app->get('/', function () use ($app) {
    $categories = $app['dao.category']->findAll();
    return $app['twig']->render('index.html.twig',array('categories' => $categories));
})->bind('home');

// Category page
$app->get('/category/{id}', function ($id) use ($app) {
    $vinyls = $app['dao.vinyl']->findAllByCategory($id);
    $categories = $app['dao.category']->findAll();
    $category=$app['dao.category']->find($id);
    return $app['twig']->render('category.html.twig',array('vinyls' => $vinyls, 'categories' => $categories, 'category' => $category));
})->bind('category');

// vinyl page
$app->get('/vinyl/{id}', function($id) use ($app){
    $categories = $app['dao.category']->findAll();
    $vinyl = $app['dao.vinyl']->find($id);
    return $app['twig']->render('vinyl.html.twig',array('categories' => $categories, 'vinyl' => $vinyl));
})->bind('vinyl');

// Login form
$app->get('/login', function(Request $request) use ($app) {
    $categories = $app['dao.category']->findAll();
    return $app['twig']->render('login.html.twig', array(
        'error'         => $app['security.last_error']($request),
        'last_username' => $app['session']->get('_security.last_username'), 'categories' => $categories
    ));
})->bind('login');

// Add a user form
$app->match('/inscription', function(Request $request) use ($app) {
    $categories = $app['dao.category']->findAll();
    $user = new User();
    $userForm = $app['form.factory']->create(new InscriptionType(), $user);
    $userForm->handleRequest($request);
    if ($userForm->isSubmitted() && $userForm->isValid()) {
        // generate a random salt value
        $salt = substr(md5(time()), 0, 23);
        $user->setSalt($salt);
        $plainPassword = $user->getPassword();
        // find the default encoder
        $encoder = $app['security.encoder.digest'];
        // compute the encoded password
        $password = $encoder->encodePassword($plainPassword, $user->getSalt());
        $user->setPassword($password); 
        $app['dao.user']->saveDefaultUser($user);
        $app['session']->getFlashBag()->add('success', 'The user was successfully created.');
        $app->redirect($app['url_generator']->generate('home'));
    }
    return $app['twig']->render('inscription_form.html.twig', array('categories' => $categories,
        'userForm' => $userForm->createView()));
})->bind('inscription');

// Admin home page
$app->get('/admin', function() use ($app) {
    $categories = $app['dao.category']->findAll();
    $vinyls = $app['dao.vinyl']->findAll();
    $users = $app['dao.user']->findAll();
    return $app['twig']->render('admin.html.twig', array(
        'categories' => $categories,
        'vinyls' => $vinyls,
        'users' => $users));
})->bind('admin');

// Modify a user form
$app->match('/modifProfil/{id}', function(Request $request, $id) use ($app) {
    $categories = $app['dao.category']->findAll();
    $user = $app['dao.user']->find($id);
    $profilForm = $app['form.factory']->create(new ProfilType(), $user);
    $profilForm->handleRequest($request);
    if ($profilForm->isSubmitted() && $profilForm->isValid()) {
        // generate a random salt value
        $salt = substr(md5(time()), 0, 23);
        $user->setSalt($salt);
        $plainPassword = $user->getPassword();
        // find the default encoder
        $encoder = $app['security.encoder.digest'];
        // compute the encoded password
        $password = $encoder->encodePassword($plainPassword, $user->getSalt());
        $user->setPassword($password);
        $app['dao.user']->saveDefaultUser($user);
        $app['session']->getFlashBag()->add('success', 'The user was successfully modified.');
        $app->redirect($app['url_generator']->generate('home'));
    }
    return $app['twig']->render('profil_form.html.twig', array('user' => $user, 'categories' => $categories,
        'profilForm' => $profilForm->createView()));
})->bind('modifProfil');

// Profil
$app->get('/profil/{id}', function ($id) use ($app) {
    $categories = $app['dao.category']->findAll();
    $user = $app['dao.user']->find($id);
    return $app['twig']->render('profil.html.twig',array('categories' => $categories, 'user' => $user));
})->bind('profil');

// Basket page
$app->get('/panier/{id}', function ($id) use ($app){
    $categories = $app['dao.category']->findAll();
    $baskets = $app['dao.basket']->findAllByIdUser($id);

    return $app['twig']->render('basket.html.twig', array('categories'=>$categories, 'baskets' => $baskets));  
})->bind('basket');