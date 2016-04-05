<?php

namespace CoolwayFestivales\BackendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FeastStageArtistType extends AbstractType
{
    private $filtro;
    private $rango_artistas;

    public function __construct($filtro, $rango_artistas)
    {
        $this->filtro = $filtro;
        $this->rango_artistas = $rango_artistas;
    }
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /*
        ->add('artist', null, array('label' => 'Artista'))
        ->add('feast_stage', null, array('label' => 'Escenario de Festival'))
        */
        $builder
            ->add('artist', 'entity', array(
                'label'   => 'Artista',
                'class' => 'BackendBundle:Artist',
                'query_builder' => function($repository)
                {
                    return $repository->createQueryBuilder('a')->andWhere('a.id IN (:rango)')->setParameter('rango', $this->rango_artistas, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY);
                }
            ))
            ->add('feast_stage', 'entity', array(
                'label'   => 'Escenario de Festival',
                'class' => 'BackendBundle:FeastStage',
                'query_builder' => function($repository)
                {
                    return $repository->createQueryBuilder('fs')->where($this->filtro);
                }
            ))
            ->add('date', 'date', array(
                'label' => 'Fecha',
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'label_attr' => array('class' => 'date_w_default'),
                'attr' => [
                    'style' => 'text-align:center;',
                    'class' => 'form-control input-inline datepicker',
                    'data-provide' => 'datepicker',
                    'data-date-format' => 'dd/mm/yyyy'
                ]
            ))
            ->add('time', 'time', array(
                "mapped" => false,
                'widget' => 'choice',
                'label'  => "Hora",
                'attr'   => array('class' => 'time_w_default'),
                'label_attr' => array('class' => 'date_w_default')
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

} // end class