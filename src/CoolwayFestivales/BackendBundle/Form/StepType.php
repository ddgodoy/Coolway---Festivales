<?php

namespace CoolwayFestivales\BackendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class StepType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('steps', 'choice', array(
                    'label' => 'Paso',
                    'attr' => array('class' => 'input-xlarge'),
                    'choices' => array(
                        '1' => '1',
                        '2' => '2',
                        '3' => '3',
                        '4' => '4'
                    )
                ))
                ->add('text', null, array('label' => 'Texto'))
                ->add('feast', null, array('label' => 'Festival'))
                ->add('enabled', null, array('label' => 'Habilitado'))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'CoolwayFestivales\BackendBundle\Entity\Step'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'coolwayfestivales_backendbundle_step';
    }

}
