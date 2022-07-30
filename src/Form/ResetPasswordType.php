<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResetPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('new_password', RepeatedType::class, [
                'type' => PasswordType::class,
                'required' => true,
                'label' => 'Nouveau mot de passe',
                'invalid_message' => 'Le mot de passe et la confirmationdoivent être identique.',
                'first_option' => [
                    'label' => 'Nouveau mot de passe',
                ],
                'second_option' => [
                    'label' => 'Confirmation du nouveau mot de passe',
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Actualiser'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
