<?php

namespace CoolwayFestivales\BackendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ArtistType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('name', null, array('label' => 'Nombre'))
            ->add('description', null, array('label' => 'Descripción'))
            ->add('id_spotify', null, array('label' => 'Spotify'))
            ->add('website', null, array('label' => 'Website'))
            ->add('twitter', null, array('label' => 'Twitter'))
            ->add('facebook', null, array('label' => 'Facebook'))
            ->add('instagram', null, array('label' => 'Instagram'))
            ->add('foto', 'file', array('label' => 'Foto', 'mapped' => false, 'required' => false))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CoolwayFestivales\BackendBundle\Entity\Artist'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'coolwayfestivales_backendbundle_artist';
    }

} // end class