<?php
use Symfony\Component\HttpFoundation\Request;
use MadeForVinyl\Domain\User;
use MadeForVinyl\Domain\Category;
use MadeForVinyl\Domain\Vinyl;
use MadeForVinyl\Form\Type\InscriptionType;
use MadeForVinyl\Form\Type\ProfilType;
use MadeForVinyl\Form\Type\VinylType;
use MadeForVinyl\Form\Type\CategoryType;
use MadeForVinyl\Domain\Basket;
use MadeForVinyl\Form\Type\UserType;
use MadeForVinyl\Form\Type\editProfilType;

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

// vinyl details page
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

// inscription of a default user form
$app->match('/inscription', function(Request $request) use ($app) {
    $categories = $app['dao.category']->findAll();
    $user = new User();
    $userForm = $app['form.factory']->create(new InscriptionType(), $user, array('app' => $app));
    $userForm->handleRequest($request);
    if ($userForm->isSubmitted() && $userForm->isValid() && $user) {
        // generate a random salt value
        $salt = substr(md5(time()), 0, 23);
        $user->setSalt($salt);
        $plainPassword = $user->getPassword();
        // find the default encoder
        $encoder = $app['security.encoder.digest'];
        // compute the encoded password
        $password = $encoder->encodePassword($plainPassword, $user->getSalt());
        $user->setPassword($password); 
        $user->setRole('ROLE_USER');   
            $app['dao.user']->save($user);
            $app->redirect($app['url_generator']->generate('home'));
    
    }
    return $app['twig']->render('inscription_form.html.twig', array('categories' => $categories,
        'userForm' => $userForm->createView()));
})->bind('inscription');

// Profil
$app->get('/profil/{id}', function ($id) use ($app) {
    $categories = $app['dao.category']->findAll();
    $user = $app['dao.user']->find($id);
    return $app['twig']->render('profil.html.twig',array('categories' => $categories, 'user' => $user));
})->bind('profil');

// Edit a Default user form
$app->match('/edit_Profil/{id}', function(Request $request, $id) use ($app) {
    $categories = $app['dao.category']->findAll();
    $user = $app['dao.user']->find($id);
    $profilForm = $app['form.factory']->create(new ProfilType(), $user,array('app' => $app));
    $profilForm->handleRequest($request);
    if ($profilForm->isSubmitted() && $profilForm->isValid()) {
       $plainPassword = $user->getPassword();
        // find the encoder for the user
        $encoder = $app['security.encoder_factory']->getEncoder($user);
        // compute the encoded password
        $password = $encoder->encodePassword($plainPassword, $user->getSalt());
        $user->setPassword($password);
        $app['dao.user']->save($user);
        $app['session']->getFlashBag()->add('success', "Vos données ont bien été modifiés");
        $app->redirect($app['url_generator']->generate('home'));
    }
    return $app['twig']->render('profil_form.html.twig', array(
        'user' => $user, 
        'categories' => $categories,
        'title' => "Modifier vos données",
        'profilForm' => $profilForm->createView()));
})->bind('edit_Profil');

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

// Add a vinyl form
$app->match('/admin/vinyl/add', function(Request $request) use ($app) {
    $categories = $app['dao.category']->findAll();
    // create the vinyl
    $vinyl = new Vinyl();
    $vinylForm = $app['form.factory']->create(new VinylType($categories), $vinyl);
    $vinylForm->handleRequest($request);
    if ($vinylForm->isSubmitted() && $vinylForm->isValid()) {
        $categoryId = $vinylForm->get("category")->getData();
        $category = $app['dao.category']->find($categoryId);
        $vinyl->setCategory($category);
        $app['dao.vinyl']->save($vinyl);
        $app['session']->getFlashBag()->add('success', "Le vinyl a bien été créé");
    }
    return $app['twig']->render('vinyl_form.html.twig', array(
        'title' => "Ajout Vinyl",        
        'vinylForm' => $vinylForm->createView(), 
        'categories' => $categories));
})->bind('admin_vinyl_add');

// Add a user form
$app->match('/admin/user/add', function(Request $request) use ($app){
    $categories = $app['dao.category']->findAll();
    $user = new User();
    $userForm = $app['form.factory']->create(new UserType(), $user,array('app' => $app));
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
        $app['dao.user']->save($user);
        $app['session']->getFlashBag()->add('success', "L'utilisateur a bien été créé");
    }
    return $app['twig']->render('user_form.html.twig', array(
        'title' => "Ajout Utilisateur",        
        'userForm' => $userForm->createView(), 
        'categories' => $categories));
})->bind('admin_user_add');

// Add a category form
$app->match('/admin/category/add', function(Request $request) use ($app) {
    $categories = $app['dao.category']->findAll();
    // create the vinyl
    $category = new Category();
    $categoryForm = $app['form.factory']->create(new CategoryType(), $category);
    $categoryForm->handleRequest($request);
    if ($categoryForm->isSubmitted() && $categoryForm->isValid()) {
        $app['dao.category']->save($category);
        $app['session']->getFlashBag()->add('success', "La catégorie a bien été créée");
    }
    return $app['twig']->render('category_form.html.twig', array(
        'title' => "Ajout Catégorie",        
        'categoryForm' => $categoryForm->createView(), 
        'categories' => $categories));
})->bind('admin_category_add');

// Remove a vinyl
$app->get('/admin/vinyl/{id}/delete', function($id, Request $request) use ($app) {
    // Delete the vinyl
    $app['dao.vinyl']->delete($id);
    $app['session']->getFlashBag()->add('success', 'Le vinyl a bien été supprimé.');
    // Redirect to admin home page
    return $app->redirect($app['url_generator']->generate('admin'));
})->bind('admin_vinyl_delete');

// Remove a user
$app->get('/admin/user/{id}/delete', function($id, Request $request) use ($app) {
    //Delete baskets of the user
    $app['dao.basket']->deleteByUser($id);
    // Delete the user
    $app['dao.user']->delete($id);
    $app['session']->getFlashBag()->add('success', "L'utilisateur a bien été supprimé.");
    // Redirect to admin home page
    return $app->redirect($app['url_generator']->generate('admin'));
})->bind('admin_user_delete');

// Remove a category
$app->get('/admin/category/{id}/delete', function($id, Request $request) use ($app) {
    // Delete all vinyl which have the same category
    $app['dao.vinyl']->deleteByCategory($id);
    // Delete the category
    $app['dao.category']->delete($id);
    $app['session']->getFlashBag()->add('success', "La catégorie a bien été supprimée.");
    // Redirect to admin home page
    return $app->redirect($app['url_generator']->generate('admin'));
})->bind('admin_category_delete');

// edit a user form
$app->match('/admin/user/{id}/edit', function(Request $request, $id) use ($app) {
    $categories = $app['dao.category']->findAll();
    $user = $app['dao.user']->find($id);
    $userForm = $app['form.factory']->create(new editProfilType(), $user,array('app' => $app));
    $userForm->handleRequest($request);
    if ($userForm->isSubmitted() && $userForm->isValid()) {
        $plainPassword = $user->getPassword();
        // find the encoder for the user
        $encoder = $app['security.encoder_factory']->getEncoder($user);
        // compute the encoded password
        $password = $encoder->encodePassword($plainPassword, $user->getSalt());
        $user->setPassword($password);
        $app['dao.user']->save($user);
        $app['session']->getFlashBag()->add('success', "L'utilisateur a bien été modifié");
        $app->redirect($app['url_generator']->generate('home'));
    }
    return $app['twig']->render('editProfil_form.html.twig', array(
        'user' => $user, 
        'categories' => $categories,
        'title' => "Modification Utilisateur",
        'userForm' => $userForm->createView()));
})->bind('admin_user_edit');

// edit a vinyl form
$app->match('/admin/vinyl/{id}/edit', function(Request $request, $id) use ($app) {
    $categories = $app['dao.category']->findAll();
    $vinyl = $app['dao.vinyl']->find($id);
    $vinylForm = $app['form.factory']->create(new VinylType($categories), $vinyl);
    $vinylForm->handleRequest($request);
    if ($vinylForm->isSubmitted() && $vinylForm->isValid()) {
        $categoryId = $vinylForm->get("category")->getData();
        $category = $app['dao.category']->find($categoryId);
        $vinyl->setCategory($category);
        $app['dao.vinyl']->save($vinyl);
        $app['session']->getFlashBag()->add('success', "Le vinyl a bien été modifié");
    }
    return $app['twig']->render('vinyl_form.html.twig', array(
        'vinyl' => $vinyl, 
        'categories' => $categories,
        'title' => "Modification Vinyl",
        'vinylForm' => $vinylForm->createView()));
})->bind('admin_vinyl_edit');

// edit a category form
$app->match('/admin/category/{id}/edit', function(Request $request, $id) use ($app) {
    $categories = $app['dao.category']->findAll();
    $category = $app['dao.category']->find($id);
    $categoryForm = $app['form.factory']->create(new CategoryType(), $category);
    $categoryForm->handleRequest($request);
    if ($categoryForm->isSubmitted() && $categoryForm->isValid()) {
        $app['dao.category']->save($category);
        $app['session']->getFlashBag()->add('success', "La catégorie a bien été modifiée");
        $app->redirect($app['url_generator']->generate('home'));
    }
    return $app['twig']->render('category_form.html.twig', array(
        'category' => $category, 
        'categories' => $categories,
        'title' => "Modification Category",
        'categoryForm' => $categoryForm->createView()));
})->bind('admin_category_edit');

// Basket page
$app->get('/panier/{id}', function ($id) use ($app){
    $categories = $app['dao.category']->findAll();
    $baskets = $app['dao.basket']->findAllByIdUser($id);

    return $app['twig']->render('basket.html.twig', array('categories'=>$categories, 'baskets' => $baskets));  
})->bind('basket');

// Add in a basket
$app->get('/ajoutPanier/{id}', function($id) use ($app){
    $basket = new Basket();
    $categories = $app['dao.category']->findAll();
    $vinyl = $app['dao.vinyl']->find($id);
    $basket->setVinyl($vinyl);
    $basket->setOwner($app['user']); //return connected user
    $app['dao.basket']->save($basket); 
    $app['session']->getFlashBag()->add('success', "Le vinyl a bien été enregistré dans votre panier");

    return $app['twig']->render('vinyl.html.twig',array('categories' => $categories, 'vinyl' => $vinyl));
})->bind('ajoutPanier');