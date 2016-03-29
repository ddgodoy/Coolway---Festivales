<?php

namespace CoolwayFestivales\BackendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TermsType extends AbstractType
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
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'CoolwayFestivales\BackendBundle\Entity\Terms'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'coolwayfestivales_backendbundle_terms';
    }

} // end class