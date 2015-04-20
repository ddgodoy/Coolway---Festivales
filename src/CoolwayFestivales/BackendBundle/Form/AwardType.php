<?php

namespace CoolwayFestivales\BackendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AwardType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('name', null, array('label' => 'Nombre'))
                ->add('image', 'file', array('label' => 'Imagen', "required" => ""))
                ->add('terms_conditions', null, array('label' => 'Términos y Condiciones'))
                ->add('feast', null, array('label' => 'Festival'))
                ->add('enabled', null, array('label' => 'Habilitado'))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'CoolwayFestivales\BackendBundle\Entity\Award'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'coolwayfestivales_backendbundle_award';
    }

}
