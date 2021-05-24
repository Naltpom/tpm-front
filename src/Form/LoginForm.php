<?php

declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class LoginForm.
 */
class LoginForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', null, [
                'attr' => [
                    'placeholder' => 'label.username',
                ],
                'label' => false,
            ])

            ->add('password', PasswordType::class, [
                'attr' => [
                    'placeholder' => 'label.password',
                ],
                'label' => false,
            ])
        ;
    }

    public function getBlockPrefix()
    {
        return '';
    }
}
