<?php

namespace CoolwayFestivales\SafetyBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class UserType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('username')
                ->add('name')
                ->add('email')
                ->add('enabled')
                ->add('password')
//                ->add('password', 'repeated', array(
//                    'first_name' => 'password',
//                    'second_name' => 'confirmar',
//                    'type' => 'password'))
                ->add('user_roles');
    }

    public function getName() {
        return 'prodi_safetybundle_usertype';
    }

}
