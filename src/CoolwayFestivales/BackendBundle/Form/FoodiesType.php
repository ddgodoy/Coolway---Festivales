<?php

namespace CoolwayFestivales\BackendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FoodiesType extends AbstractType
{
    private $filtro;
    private $required_foto;
    private $required_portada;

    public function __construct($aFlag, $filtro)
    {
        $this->filtro = $filtro;
        $this->required_foto = $aFlag['foto'];
        $this->required_portada = $aFlag['portada'];
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
            ->add('name', null, array('label' => 'Nombre'))
            ->add('description', null, array('label' => 'Descripción'))
            ->add('website', null, array(
                'label' => 'Website',
                'attr' => array('placeholder' => 'Dirección del sitio web, ej: http://www.angelstanich.com')
            ))
            ->add('twitter', null, array(
                'label' => 'Twitter',
                'attr' => array('placeholder' => 'Identificador del usuario en Twitter, ej: angelstanich')
            ))
            ->add('facebook', null, array(
                'label' => 'Facebook',
                'attr' => array('placeholder' => 'Identificador del usuario en Facebook, ej: angel.stanich.oficial')
            ))
            ->add('instagram', null, array(
                'label' => 'Instagram',
                'attr' => array('placeholder' => 'Identificador del usuario en Instagram, ej: grupmanel')
            ))
            ->add('foto', 'file', array('label' => 'Foto', 'mapped' => false, 'required' => $this->required_foto))
            ->add('portada', 'file', array('label' => 'Portada', 'mapped' => false, 'required' => $this->required_portada))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CoolwayFestivales\BackendBundle\Entity\Foodies'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'coolwayfestivales_backendbundle_foodies';
    }

} // end class