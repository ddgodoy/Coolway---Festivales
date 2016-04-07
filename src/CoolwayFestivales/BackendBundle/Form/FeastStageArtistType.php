<?php

namespace CoolwayFestivales\BackendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FeastStageArtistType extends AbstractType
{
    private $filtro;
    private $accion;
    private $rango_artistas;
    private $default_hora;

    public function __construct($filtro, $accion, $rango_artistas, $default_hora)
    {
        $this->filtro = $filtro;
        $this->accion = $accion;
        $this->rango_artistas = $rango_artistas;
        $this->default_hora = $default_hora;
    }
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
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
        ;
        if ($this->accion == 'editar')
        {
            $builder
                ->add('date', 'date', array(
                    'label' => 'Fecha [dd/mm/yyyy]',
                    'widget' => 'choice',
                    'attr'   => array('class' => 'time_lw_default'),
                    'label_attr' => array('class' => 'date_lw_default')
                ));
        } else {
            $builder
                ->add('date', 'date', array(
                    'label' => 'Fecha [dd/mm/yyyy]',
                    'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy',
                    'label_attr' => array('class' => 'date_lw_default'),
                    'attr' => [
                        'style' => 'text-align:center;',
                        'class' => 'form-control input-inline datepicker',
                        'data-provide' => 'datepicker',
                        'data-date-format' => 'dd/mm/yyyy'
                    ]
                ));
        }
        $builder
            ->add('time', 'time', array(
                "mapped" => false,
                'widget' => 'choice',
                'label'  => "Horario [hr/minutos]",
                'attr'   => array('class' => 'time_lw_default'),
                'label_attr' => array('class' => 'date_lw_default'),
                'data' => $this->default_hora
            ));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CoolwayFestivales\BackendBundle\Entity\FeastStageArtist'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'coolwayfestivales_backendbundle_feaststageartist';
    }

} // end class