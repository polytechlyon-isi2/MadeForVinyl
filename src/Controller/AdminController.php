<?php

namespace MadeForVinyl\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use MadeForVinyl\Domain\Category;
use MadeForVinyl\Form\Type\CategoryType;
use MadeForVinyl\Domain\Vinyl;
use MadeForVinyl\Form\Type\VinylType;
use MadeForVinyl\Domain\User;
use MadeForVinyl\Form\Type\UserType;
use MadeForVinyl\Form\Type\editProfilType;

class AdminController {


    /**
     * List of all categories for all controllers.
     *
     * @param Application $app Silex application
     */
    public function getCategories(Application $app){
        return $app['dao.category']->findAll();  
    }

    /**
     * admin page controller.
     *
     * @param Application $app Silex application
     */
    public function indexAction(Application $app){
        $vinyls = $app['dao.vinyl']->findAll();
        $users = $app['dao.user']->findAll();
        return $app['twig']->render('admin.html.twig', array(
        'categories' => AdminController::getCategories($app),
        'vinyls' => $vinyls,
        'users' => $users));
    }

    /**
     * add a vinyl page controller.
     *
     * @param Request $request Incoming request
     * @param Application $app Silex application
     */
    public function addVinylAction(Request $request, Application $app){ 
        // create the vinyl
        $vinyl = new Vinyl();
        $vinylForm = $app['form.factory']->create(new VinylType(AdminController::getCategories($app)), $vinyl);
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
            'categories' => AdminController::getCategories($app)
            )
        );
    }

    /**
     * add a user page controller.
     *
     * @param Request $request Incoming request
     * @param Application $app Silex application
     */
    public function addUserAction(Request $request, Application $app){ 
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
            'categories' => AdminController::getCategories($app)
            )
        );
    }

    /**
     * add a category page controller.
     *
     * @param Request $request Incoming request
     * @param Application $app Silex application
     */
    public function addCategoryAction(Request $request, Application $app){ 
        // create the category
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
            'categories' => AdminController::getCategories($app)
            )
        );
    }

    /**
     * remove a vinyl page controller.
     *
     * @param integer $id vinyl id
     * @param Application $app Silex application
     */
    public function deleteVinylAction($id, Application $app){ 
        // Delete the vinyl
        $app['dao.vinyl']->delete($id);
        $app['session']->getFlashBag()->add('success', 'Le vinyl a bien été supprimé.');
        // Redirect to admin home page
        return $app->redirect($app['url_generator']->generate('admin'));
    }

    /**
     * remove a user page controller.
     *
     * @param integer $id user id
     * @param Application $app Silex application
     */
    public function deleteUserAction($id, Application $app){ 
        //Delete baskets of the user
        $app['dao.basket']->deleteByUser($id);
        // Delete the user
        $app['dao.user']->delete($id);
        $app['session']->getFlashBag()->add('success', "L'utilisateur a bien été supprimé.");
        // Redirect to admin home page
        return $app->redirect($app['url_generator']->generate('admin'));
    }

    /**
     * remove a category page controller.
     *
     * @param integer $id category id
     * @param Application $app Silex application
     */
    public function deleteCategoryAction($id, Application $app){ 
        // Delete all vinyl which have the same category
        $app['dao.vinyl']->deleteByCategory($id);
        // Delete the category
        $app['dao.category']->delete($id);
        $app['session']->getFlashBag()->add('success', "La catégorie a bien été supprimée.");
        // Redirect to admin home page
        return $app->redirect($app['url_generator']->generate('admin'));
    }

    /**
     * edit a user page controller.
     *
     * @param integer $id user id
     * @param Request $request Incoming request
     * @param Application $app Silex application
     */
    public function editUserAction($id, Request $request, Application $app){ 
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
            'categories' => AdminController::getCategories($app),
            'title' => "Modification Utilisateur",
            'userForm' => $userForm->createView()
            )
        );
    }

    /**
     * edit a vinyl page controller.
     *
     * @param integer $id vinyl id
     * @param Request $request Incoming request
     * @param Application $app Silex application
     */
    public function editVinylAction($id, Request $request, Application $app){ 
        $vinyl = $app['dao.vinyl']->find($id);
        $vinylForm = $app['form.factory']->create(new VinylType(AdminController::getCategories($app)), $vinyl);
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
            'categories' => AdminController::getCategories($app),
            'title' => "Modification Vinyl",
            'vinylForm' => $vinylForm->createView()
            )
        );
    }

    /**
     * edit a category page controller.
     *
     * @param integer $id category id
     * @param Request $request Incoming request
     * @param Application $app Silex application
     */
    public function editCategoryAction($id, Request $request, Application $app){ 
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
            'categories' => AdminController::getCategories($app),
            'title' => "Modification Category",
            'categoryForm' => $categoryForm->createView()
            )
        );
    }
}
