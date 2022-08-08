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
            ->add('age', DateTimeType::class,[
                'years' => [
                    '1980' => '1980',
                    '1981' => '1981',
                    '1982' => '1982',
                    '1983' => '1983',
                    '1984' => '1984',
                    '1985' => '1985',
                    '1986' => '1986',
                    '1987' => '1987',
                    '1988' => '1988',
                    '1989' => '1989',
                    '1990' => '1990',
                    '1991' => '1991',
                    '1992' => '1992',
                    '1993' => '1993',
                    '1994' => '1994',
                    '1995' => '1995',
                    '1996' => '1996',
                    '1997' => '1997',
                    '1998' => '1998',
                    '1999' => '1999',
                    '2000' => '2000',
                    '2001' => '2001',
                    '2002' => '2002',
                    '2003' => '2003',
                    '2004' => '2004',
                    '2005' => '2005',
                    '2006' => '2006',
                    '2007' => '2007',
                    '2008' => '2008',
                    '2009' => '2009',
                    '2010' => '2010',
                    '2011' => '2011',
                    '2012' => '2012',
                    '2013' => '2013',
                    '2014' => '2014',
                    '2015' => '2015',
                    '2016' => '2016',
                    '2017' => '2017',
                    '2018' => '2018',
                    '2019' => '2019',
                    '2020' => '2020',
                    '2021' => '2021',
                    '2022' => '2022'
                ]

            ])
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
