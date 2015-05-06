<?php

namespace CoolwayFestivales\BackendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FeastType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('name', null, array('label' => 'Nombre'))
                ->add('latitude', null, array('label' => 'Latitud'))
                ->add('longitude', null, array('label' => 'Longitud'))
                ->add('date_from', null, array(
                    'label' => 'Desde:',
                    'widget' => 'single_text',
                    'format' => 'MM/dd/yyyy',
                    'attr' => array(
                        'class' => 'datepicker',
                    )
                ))
                ->add('date_to', null, array(
                    'label' => 'Hasta:',
                    'widget' => 'single_text',
                    'format' => 'MM/dd/yyyy',
                    'attr' => array(
                        'class' => 'datepicker',
                    )
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'CoolwayFestivales\BackendBundle\Entity\Feast'
        ));
    }

    public function getName() {
        return 'coolway_appbundle_categorytype';
    }

}
