<?php

namespace pfc\InicioBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ContactoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('nombre')
                ->add('email', 'email')
                ->add('asunto')
                ->add('mensaje', 'textarea');
    }

    public function getName()
    {
        return 'contacto';
    }
}