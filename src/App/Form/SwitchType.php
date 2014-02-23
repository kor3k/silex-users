<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SwitchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add( 'save' , 'submit' , [ 'label'  =>  'save' ] )
            ->add( 'users' , 'collection' ,
                [
                    'type'	=>  new SwitchUserType() ,
                ]
            )
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            [
                'method' => 'PUT'
            ]);
    }

    public function getName()
    {
        return 'switch';
    }
}
