<?php
use MadeForVinyl\Domain\Basket;

// Home page
$app->get('/', "MadeForVinyl\Controller\HomeController::indexAction")->bind('home');

// Category page
$app->get('/category/{id}', "MadeForVinyl\Controller\HomeController::categoryAction")->bind('category');

// vinyl details page
$app->get('/vinyl/{id}', "MadeForVinyl\Controller\HomeController::vinylAction")->bind('vinyl');

// Login form
$app->get('/login', "MadeForVinyl\Controller\HomeController::loginAction")->bind('login');

// inscription of a default user form
$app->match('/inscription', "MadeForVinyl\Controller\HomeController::suscribeAction")->bind('inscription');

// Profil
$app->get('/profil/{id}', "MadeForVinyl\Controller\HomeController::profilAction")->bind('profil');

// Edit profil form
$app->match('/edit_Profil/{id}', "MadeForVinyl\Controller\HomeController::editProfilAction")->bind('edit_Profil');

// Admin home page
$app->get('/admin', "MadeForVinyl\Controller\AdminController::indexAction")->bind('admin');

// Add a vinyl form
$app->match('/admin/vinyl/add', "MadeForVinyl\Controller\AdminController::addVinylAction")->bind('admin_vinyl_add');

// Add a user form
$app->match('/admin/user/add', "MadeForVinyl\Controller\AdminController::addUserAction")->bind('admin_user_add');

// Add a category form
$app->match('/admin/category/add', "MadeForVinyl\Controller\AdminController::addCategoryAction")->bind('admin_category_add');

// Remove a vinyl
$app->get('/admin/vinyl/{id}/delete', "MadeForVinyl\Controller\AdminController::deleteVinylAction")->bind('admin_vinyl_delete');

// Remove a user
$app->get('/admin/user/{id}/delete', "MadeForVinyl\Controller\AdminController::deleteUserAction")->bind('admin_user_delete');

// Remove a category
$app->get('/admin/category/{id}/delete', "MadeForVinyl\Controller\AdminController::deleteCategoryAction")->bind('admin_category_delete');

// edit a user form
$app->match('/admin/user/{id}/edit', "MadeForVinyl\Controller\AdminController::editUserAction")->bind('admin_user_edit');

// edit a vinyl form
$app->match('/admin/vinyl/{id}/edit', "MadeForVinyl\Controller\AdminController::editVinylAction")->bind('admin_vinyl_edit');

// edit a category form
$app->match('/admin/category/{id}/edit', "MadeForVinyl\Controller\AdminController::editCategoryAction")->bind('admin_category_edit');

// Basket page
$app->get('/panier/{id}', function ($id) use ($app){
    $categories = $app['dao.category']->findAll();
    $baskets = $app['dao.basket']->findAllByIdUser($id);

    return $app['twig']->render('basket.html.twig', array('categories'=>$categories, 'baskets' => $baskets));  
})->bind('basket');

// Add in a basket
$app->get('/ajoutPanier/{id}', function($id) use ($app){
    $baskets = $app['dao.basket']->findAllByIdUser($app['user']->getId());
    $categories = $app['dao.category']->findAll();
    $vinyl = $app['dao.vinyl']->find($id);
    foreach ($baskets as $basket){
        if ($vinyl->getId() == $basket->getVinyl()->getId()){
            $basket->setQuantity($basket->getQuantity()+1);
            $app['dao.basket']->save($basket); 
        }
    }
    if(empty($baskets)){
        $newBasket = new Basket();
        $newBasket->setQuantity(1);
        $newBasket->setVinyl($vinyl);
        $newBasket->setOwner($app['user']); //return connected user
        $app['dao.basket']->save($newBasket); 
    }
    $app['session']->getFlashBag()->add('success', "Le vinyl a bien été enregistré dans votre panier");

    return $app['twig']->render('vinyl.html.twig',array('categories' => $categories, 'baskets' => $baskets, 'vinyl' => $vinyl));
})->bind('ajoutPanier');