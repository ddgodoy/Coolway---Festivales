<?php

namespace CoolwayFestivales\BackendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserFeastDataType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('feast', null, array('label' => 'Festival'))
                ->add('user', null, array('label' => 'Usuario'))
                ->add('total', null, array('label' => 'Total'))
                ->add('dance')
                ->add('music', null, array('label' => 'Musica'))
                ->add('total_share')
                ->add('latitude', null, array('label' => 'Latitud'))
                ->add('longitude', null, array('label' => 'Longitud'))

        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'CoolwayFestivales\BackendBundle\Entity\UserFeastData'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'coolwayfestivales_backendbundle_userfeastdata';
    }

}
