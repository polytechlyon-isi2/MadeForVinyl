<?php

namespace MadeForVinyl\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class VinylType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text',array('label'=>'Titre'))
            ->add('artist', 'text',array('label'=>'Artiste'))
            ->add('year', 'integer',array('label'=>'Année'))
            ->add('price', 'text',array('label'=>'Prix'))
            ->add('sleeve', 'text',array('label'=>'Url Image'))
            ->add('category','text',array('label'=>'Categorie'));
    }

    public function getName()
    {
        return 'vinyl';
    }
}