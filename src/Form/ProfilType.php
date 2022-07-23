<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;

class ProfilType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if ($options['current_password_is_required']) {
            $builder
                ->add('currentPassword', PasswordType::class, [
                    'label' => 'Entrer votre mot de passe',
                    'attr' => [
                        'autocomplete' => 'off'
                    ],
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Veuillez entrer un mot de passe',
                        ]),
                        new UserPassword([
                            'message' => 'Mot de passe invalide',
                        ]),
                    ]
                ]);
        }
        $builder
            ->add('plainEmail', RepeatedType::class, [
                'type' => EmailType::class,
                'first_options' => [
                    'label' => 'Entrer votre nouvel email',
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Veuillez entrer un email',
                        ]),
                    ],
                ],
                'second_options' => [
                    'label' => 'Confirmer votre nouvel email',
                ],
                'invalid_message' => 'Les champs nouvels email doivent être identiques.',
                'mapped' => false,
            ])
            ->add('firstname', TextType::class, [
                'label' => 'Entrer votre prénom',
            ])
            ->add('name', TextType::class, [
                'label' => 'Entrer votre nom',
            ])
            ->add('gender', ChoiceType::class, [
                'label' => 'Sexe',
                'choices' => [
                    'Homme' => 'Homme',
                    'Femme' => 'Femme'
                ]
            ])
            ->add('age', DateTimeType::class)
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => [
                    'label' => 'Entrer votre nouveau mot de passe',
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Veuillez entrer un mot de passe',
                        ]),
                        new Length([
                            'min' => 6,
                            'minMessage' => 'Votre mot de passe doit être au moins {{ limit }} caractères',
                            // max length allowed by Symfony for security reasons
                            'max' => 4096,
                        ]),
                    ],
                ],
                'second_options' => [
                    'label' => 'Confirmer votre nouveau mot de passe',
                ],
                'invalid_message' => 'Les champs nouveaux mots de passe doivent être identiques.',
                'mapped' => false,
            ])
            ->add('avatar', FileType::class, [
                'label' => 'Image (JPEG/PNG)',
                'data_class' => null,
                'required' => false
            ])
            ->add('description', TextareaType::class, [
                'label' => "Description",
                'required' => false

            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'current_password_is_required' => false
        ]);
        
        $resolver->setDefaults([
            // 'data_class' => User::class,
        ]);

        $resolver->setAllowedTypes('current_password_is_required', 'bool');
    }
}
