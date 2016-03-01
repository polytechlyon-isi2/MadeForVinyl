<?php

// Home page
$app->get('/', function () use ($app) {
    $categories = $app['dao.category']->findAll();
    return $app['twig']->render('index.html.twig',array('categories' => $categories));
})->bind('home');

// Category page
$app->get('/category/{id}', function ($id) use ($app) {
    $vinyls = $app['dao.vinyl']->findAllId($id);
    $categories = $app['dao.category']->findAll();
    $category=$app['dao.category']->find($id);
    return $app['twig']->render('category.html.twig',array('vinyls' => $vinyls, 'categories' => $categories, 'category' => $category));
})->bind('category');

// vinyl page
$app->get('/category/vinyl/{id}', function($id) use ($app){
    $categories = $app['dao.category']->findAll();
    $vinyl = $app['dao.vinyl']->find($id);
    return $app['twig']->render('vinyl.html.twig',array('categories' => $categories, 'vinyl' => $vinyl));
})->bind('vinyl');