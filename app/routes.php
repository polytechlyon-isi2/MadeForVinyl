<?php

// Home page
$app->get('/', function () use ($app) {
    return $app['twig']->render('index.html.twig');
});

// Category page
$app->get('/', function () use ($app) {
    $vinyl = $app['dao.vinyl']->findAll();
    return $app['twig']->render('category.html.twig', array('vinyl' => $vinyl));
});