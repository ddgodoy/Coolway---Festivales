<?php

namespace CoolwayFestivales\BackendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ImagesType extends AbstractType
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
        $builder
            ->add('feast', 'entity', array(
                'label'   => 'Festival',
                'class' => 'BackendBundle:Feast',
                'query_builder' => function($repository)
                {
                    return $repository->createQueryBuilder('f')->where($this->filtro);
                }
            ))
            ->add('cartel', 'file', array('label' => 'Imagen', 'mapped' => false, 'required' => false))
            ->add('enabled', null, array('label' => 'Habilitado'))
            ->add('code_name', 'hidden', array('data' => 'background'))
        ;
    }
    /*
     * ->add('code_name', 'choice', array('choices' => array('plano' => 'Plano', 'background' => 'Background'), 'label' => 'Nombre de código'))
     */

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CoolwayFestivales\BackendBundle\Entity\Images'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'coolwayfestivales_backendbundle_images';
    }

} // end class