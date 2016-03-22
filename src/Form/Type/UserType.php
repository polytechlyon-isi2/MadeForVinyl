<?php

namespace MadeForVinyl\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text',array('label'=>'Prénom'))
            ->add('surname', 'text',array('label'=>'Nom'))
            ->add('adress', 'text',array('label'=>'Adresse'))
            ->add('postalCode', 'integer',array('label'=>'Code Postal'))
            ->add('town', 'text',array('label'=>'Ville'))
            ->add('username', 'text',array('label'=>'Adresse mail'))
            ->add('password', 'repeated', array(
                'type'            => 'password',
                'invalid_message' => 'The password fields must match.',
                'options'         => array('required' => true),
                'first_options'   => array('label' => 'Mot de passe'),
                'second_options'  => array('label' => 'Répéter Mot de passe')
            ))
            ->add('role', 'choice', array(
                'choices' => array('ROLE_ADMIN' => 'Admin', 'ROLE_USER' => 'User')
            ));
    }

    public function getName()
    {
        return 'user';
    }
}