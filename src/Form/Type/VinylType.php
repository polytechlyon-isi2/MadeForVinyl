<?php

namespace MadeForVinyl\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class VinylType extends AbstractType
{
    private $categories;
     /**
     * Constructeur.
     *
     * @param array $categories Liste des catégories
     */
    public function __construct(array $categories)
    {
        $this->categories = $categories;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
       // Transforme le tableau id => objet en un tableau chaîne => id pour la liste déroulante
        $choices = array();
        foreach ($this->categories as $id => $category) {
            $cle = $category->getTitle();
            $choices[$cle] = $id;
        }
        
        $builder
            ->add('title', 'text',array('label'=>'Titre'))
            ->add('artist', 'text',array('label'=>'Artiste'))
            ->add('year', 'integer',array('label'=>'Année'))
            ->add('price', 'text',array('label'=>'Prix'))
            ->add('sleeve', 'text',array('label'=>'Url Image'))
            ->add('category','choice',array(
                'choices' => $choices,
                'choices_as_values' => true, // Future valeur par défaut dans Symfony 3.x
                'choice_value' => function ($choice) {
                    return $choice;
                },
                'expanded' => false, 
                'multiple' => false,
                'mapped' => false  // ce champ n'est pas mis en correspondance avec la propriété de l'objet
            ));
    }
    
    public function getName()
    {
        return 'vinyl';
    }
}