<?php

namespace pfc\InicioBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UsuarioModificarType extends AbstractType
{       
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('nombre'   ,null,array('required' => true))
                ->add('apellidos',null,array('required' => false))
                ->add('fecha_nacimiento', 'birthday',array('widget' => 'choice'))            
                ->add('password', 'repeated', array(
                     'type'             => 'password'
                    ,'invalid_message'  => 'Las dos contraseñas deben coincidir.'
                    ,'options'          => array('attr' => array('class' => 'password-field'))
                    ,'required'         => false
                    ,'first_options'    => array('label' => 'Contraseña')
                    ,'second_options'   => array('label' => 'Repetir contraseña')
                    ))       
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
//        $resolver->setDefaults(array('data_class' => 'Symfony\Component\Security\Core\User\User'));
//        $resolver->setDefaults(array('data_class' => null));
//        $resolver->setDefaults(array('data_class' => 'pfc\InicioBundle\Entity\Usuario'));
    }

    public function getName()
    {
        return 'usuario_modificar';
    }
}