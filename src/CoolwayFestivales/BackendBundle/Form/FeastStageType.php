<?php

namespace CoolwayFestivales\BackendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FeastStageType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('feast', null, array('label' => 'Festival'))
                ->add('stage', null, array('label' => 'Etapa'))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'CoolwayFestivales\BackendBundle\Entity\FeastStage'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'coolwayfestivales_backendbundle_feaststage';
    }

}
