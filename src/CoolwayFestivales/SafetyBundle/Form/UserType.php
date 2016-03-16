<?php

namespace CoolwayFestivales\SafetyBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('username', null, array('label' => 'Usuario'))
                ->add('name', null, array('label' => 'Nombre'))
                ->add('email', null, array('label' => 'Correo'))
                ->add('password', null, array('label' => 'Contraseña'))
//                ->add('password', 'repeated', array(
//                    'first_name' => 'password',
//                    'second_name' => 'confirmar',
//                    'type' => 'password'))
                ->add('feast', null, array('label' => 'Festival'))
                ->add('user_roles', null, array('label' => 'Rol')
            );
    }
    //
    public function getName() {
        return 'coolway_safetybundle_usertype';
    }

} // end class