<?php

namespace CoolwayFestivales\BackendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FeastStageType extends AbstractType
{
    private $filtro;

    public function __construct($filtro, $accion)
    {
        $this->filtro = $filtro;
        $this->tipoCampo = null;
        $this->tipoArray['label'] = 'Escenario';

        if ($accion == 'crear') {
            $this->tipoCampo = 'text';
            $this->tipoArray['mapped'] = false;
        }
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
            ->add('stage', $this->tipoCampo, $this->tipoArray);
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'CoolwayFestivales\BackendBundle\Entity\FeastStage'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'coolwayfestivales_backendbundle_feaststage';
    }

} // end class