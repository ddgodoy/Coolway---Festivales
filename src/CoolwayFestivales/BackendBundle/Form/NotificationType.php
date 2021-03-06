<?php

namespace CoolwayFestivales\BackendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class NotificationType extends AbstractType
{
    private $filtro;
    private $accion;
    private $default_hora;

    public function __construct($filtro, $accion, $default_hora)
    {
        $this->filtro = $filtro;
        $this->accion = $accion;
        $this->default_hora = $default_hora;
    }
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('name', null, array('label' => 'Título'))
            ->add('text', null, array('label' => 'Contenido'))
            ->add('feast', 'entity', array(
                'label'   => 'Festival',
                'class' => 'BackendBundle:Feast',
                'query_builder' => function($repository)
                {
                    return $repository->createQueryBuilder('f')->where($this->filtro);
                }
            ))
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
            'data_class' => 'CoolwayFestivales\BackendBundle\Entity\Notification'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'coolwayfestivales_backendbundle_notification';
    }

} // end class