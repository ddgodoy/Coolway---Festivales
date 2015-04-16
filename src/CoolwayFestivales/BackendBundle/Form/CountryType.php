<?php

namespace CoolwayFestivales\BackendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CountryType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('name', null, array('label' => 'Nombre'));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'CoolwayFestivales\BackendBundle\Entity\Country'
        ));
    }

    public function getName() {
        return 'prodi_ecomercebundle_categorytype';
    }

}
