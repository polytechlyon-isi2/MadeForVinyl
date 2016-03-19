<?php

namespace MadeForVinyl\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class CategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text',array('label'=>'Titre'));
    }

    public function getName()
    {
        return 'category';
    }
}