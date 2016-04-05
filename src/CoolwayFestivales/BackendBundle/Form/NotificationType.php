<?php

namespace CoolwayFestivales\BackendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class NotificationType extends AbstractType
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
            'data_class' => 'CoolwayFestivales\BackendBundle\Entity\Notification'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'coolwayfestivales_backendbundle_notification';
    }

} // end class