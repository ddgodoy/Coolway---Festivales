<?php

namespace CoolwayFestivales\BackendBundle\Form;

use CoolwayFestivales\BackendBundle\Validator\Constraints\DateRange;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Callback;

class FeastType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, array('label' => 'Nombre'))
            ->add('image', 'file', array('label' => 'Imagen', 'mapped' => false, 'required' => false))
            ->add('latitude', null, array(
                'label' => 'Latitud',
                'attr' => array('placeholder' => 'grados decimales, ej: 40.7127837')
            ))
            ->add('longitude', null, array(
                'label' => 'Longitud',
                'attr' => array('placeholder' => 'grados decimales, ej: -74.0059731')
            ))
            ->add('date_from', 'date', array(
                'label' => 'Desde [dd/mm/yyyy]',
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
            ->add('date_to', 'date', array(
                'label' => 'Hasta [dd/mm/yyyy]',
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
            ->add('schedule_active', null, array('label' => 'Horarios activados'))
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