<?php

namespace MadeForVinyl\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use MadeForVinyl\Domain\Category;
use MadeForVinyl\Domain\User;
use MadeForVinyl\Form\Type\InscriptionType;
use MadeForVinyl\Form\Type\ProfilType;

class HomeController {


    /**
     * List of all categories for all controllers.
     *
     * @param Application $app Silex application
     */
    public function getCategories(Application $app){
        return $app['dao.category']->findAll();  
    }

    /**
     * Home page controller.
     *
     * @param Application $app Silex application
     */
    public function indexAction(Application $app){  
        return $app['twig']->render('index.html.twig',array(
            'categories' => HomeController::getCategories($app)
            )
        );
    }

    /**
     * Category page controller.
     *
     * @param integer $id category id
     * @param Application $app Silex application
     */
    public function categoryAction($id, Application $app){ 
        $vinyls = $app['dao.vinyl']->findAllByCategory($id);
        $category=$app['dao.category']->find($id);
        return $app['twig']->render('category.html.twig',array(
            'vinyls' => $vinyls,
            'category' => $category, 
            'categories' => HomeController::getCategories($app)
            )
        );
    }

    /**
     * Vinyl page controller.
     *
     * @param integer $id vinyl id
     * @param Application $app Silex application
     */
    public function vinylAction($id, Application $app){ 
        $vinyl = $app['dao.vinyl']->find($id);
        return $app['twig']->render('vinyl.html.twig',array(
            'vinyl' => $vinyl, 
            'categories' => HomeController::getCategories($app)
            )
        );
    }

    /**
     * login page controller.
     *
     * @param Request $request Incoming request
     * @param Application $app Silex application
     */
    public function loginAction(Request $request, Application $app){ 
        return $app['twig']->render('login.html.twig', array(
            'error'         => $app['security.last_error']($request),
            'last_username' => $app['session']->get('_security.last_username'), 
            'categories' => HomeController::getCategories($app)
            )
        );
    }

    /**
     * profil page controller.
     *
     * @param Application $app Silex application
     */
    public function profilAction($id, Application $app){ 
        $user = $app['dao.user']->find($id);
        return $app['twig']->render('profil.html.twig',array(
            'categories' => HomeController::getCategories($app),
             'user' => $user
             )
        );
    }

    /**
     * Basket page controller.
     *
     * @param integer $id user id
     * @param Application $app Silex application
     */
    public function basketAction($id, Application $app){ 
        $baskets = $app['dao.basket']->findAllByIdUser($id);
        return $app['twig']->render('basket.html.twig', array(
            'categories'=> HomeController::getCategories($app),
            'baskets' => $baskets
            )
        ); 
    }

    /**
     * Add Basket page controller.
     *
     * @param integer $id vinyl id
     * @param Application $app Silex application
     */
    public function addBasketAction($id, Application $app){
        if ($app['security.authorization_checker']->isGranted('IS_AUTHENTICATED_FULLY')) { 
            $baskets = $app['dao.basket']->findAllByIdUser($app['user']->getId());
            $categories = $app['dao.category']->findAll();
            $vinyl = $app['dao.vinyl']->find($id);
            foreach ($baskets as $basket){
                if ($vinyl->getId() == $basket->getVinyl()->getId()){
                    $basket->setQuantity($basket->getQuantity()+1);
                    $app['dao.basket']->save($basket);
                    $app['session']->getFlashBag()->add('success', "Le vinyl a bien été enregistré dans votre panier");
                    return $app['twig']->render('vinyl.html.twig',array(
                        'categories' => HomeController::getCategories($app),
                        'baskets' => $baskets, 
                        'vinyl' => $vinyl
                        )
                    ); 
                }
            }
            $newBasket = new Basket();
            $newBasket->setQuantity(1);
            $newBasket->setVinyl($vinyl);
            $newBasket->setOwner($app['user']); //return connected user
            $app['dao.basket']->save($newBasket); 
            $app['session']->getFlashBag()->add('success', "Le vinyl a bien été enregistré dans votre panier");
            return $app['twig']->render('vinyl.html.twig',array(
                'categories' => HomeController::getCategories($app),
                'baskets' => $baskets, 
                'vinyl' => $vinyl
                )
            ); 
        }
    }

    /**
     * incription user page controller.
     *
     * @param Request $request Incoming request
     * @param Application $app Silex application
     */
    public function suscribeAction(Request $request, Application $app){ 
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
        return $app['twig']->render('inscription_form.html.twig', array(
            'categories' => HomeController::getCategories($app),
            'userForm' => $userForm->createView()
            )
        );
    }

     /**
     * edit user page controller.
     *
     * @param integer $id user id
     * @param Request $request Incoming request
     * @param Application $app Silex application
     */
    public function editProfilAction($id, Request $request, Application $app){ 
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
            'categories' => HomeController::getCategories($app),
            'title' => "Modifier vos données",
            'profilForm' => $profilForm->createView()
            )
        );
    }


    


    
}
