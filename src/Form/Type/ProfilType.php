<?php

namespace MadeForVinyl\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ProfilType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
       $builder->remove('username');
    }

    public function getParent()
    {
        return new InscriptionType();
    }
    public function getName()
    {
        return 'profil';
    }
}