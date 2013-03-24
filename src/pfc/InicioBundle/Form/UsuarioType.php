<?php

namespace pfc\InicioBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UsuarioType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('email', 'email')
                ->add('password', 'repeated', array(
                     'type'             => 'password'
                    ,'invalid_message'  => 'Las dos contraseñas deben coincidir.'
                    ,'options'          => array('attr' => array('class' => 'password-field'))
                    ,'required'         => true
                    ,'first_options'    => array('label' => 'Contraseña')
                    ,'second_options'   => array('label' => 'Repetir contraseña')
                    ))                    
//                ->add('salt')
//                ->add('fecha_alta')
//                ->add('fecha_baja')
//                ->add('conexiones')
//                ->add('ultima_conexion')
                ->add('nombre')
                ->add('apellidos')
                ->add('fecha_nacimiento', 'birthday',array('widget' => 'choice'))
//                ->add('viaje_id')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array('data_class' => 'pfc\InicioBundle\Entity\Usuario'));
    }

    public function getName()
    {
        return 'registro';
    }
}