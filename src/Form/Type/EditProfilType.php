<?php

namespace MadeForVinyl\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class EditProfilType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
       $builder->remove('username');
    }

    public function getParent()
    {
        return new UserType();
    }
    public function getName()
    {
        return 'editprofil';
    }
}