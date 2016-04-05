<?php

namespace CoolwayFestivales\BackendBundle\Form;

use CoolwayFestivales\BackendBundle\Validator\Constraints\DateRange;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Callback;

class FeastType extends AbstractType
{
    /*public function __construct()
    {
        $this->test = new DateRange();
    }*/
    //
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, array('label' => 'Nombre'))
            ->add('latitude', null, array('label' => 'Latitud'))
            ->add('longitude', null, array('label' => 'Longitud'))
            ->add('date_from', 'date', array(
                'label' => 'Desde',
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
            ->add('date_to', 'date', array(
                'label' => 'Hasta',
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
        ;
    }
    //
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'  => 'CoolwayFestivales\BackendBundle\Entity\Feast',
        ));
    }
    //
    public function getName()
    {
        return 'coolway_appbundle_categorytype';
    }

} // end class