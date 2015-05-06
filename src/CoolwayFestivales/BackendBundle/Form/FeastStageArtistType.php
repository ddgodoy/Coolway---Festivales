<?php

namespace CoolwayFestivales\BackendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FeastStageArtistType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('artist', null, array('label' => 'Artista'))
                ->add('feast_stage', null, array('label' => 'Escenario de Festival'))
                ->add('date', null, array(
                    'label' => 'Fecha:',
                    'widget' => 'single_text',
                    'format' => 'MM/dd/yyyy',
                    'attr' => array(
                        'class' => 'datepicker',
            )))
                ->add('time', 'time', array(
                    'input' => 'datetime',
                    'widget' => 'choice',
                    "label" => "Hora"
                ))


        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'CoolwayFestivales\BackendBundle\Entity\FeastStageArtist'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'coolwayfestivales_backendbundle_feaststageartist';
    }

}
