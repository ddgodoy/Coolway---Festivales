<?php

namespace CoolwayFestivales\BackendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FeastStageArtistType extends AbstractType
{
    private $filtro;

    public function __construct($filtro)
    {
        $this->filtro = $filtro;
    }
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /*->add('feast_stage', null, array('label' => 'Escenario de Festival'))*/
        $builder
            ->add('artist', null, array('label' => 'Artista'))
            ->add('feast_stage', 'entity', array(
                'label'   => 'Escenario de Festival',
                'class' => 'BackendBundle:FeastStage',
                'query_builder' => function($repository)
                {
                    return $repository->createQueryBuilder('fs')->where($this->filtro);
                }
            ))
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
            ));
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

} // end class