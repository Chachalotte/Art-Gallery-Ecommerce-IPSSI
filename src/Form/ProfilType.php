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
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;

class ProfilType extends AbstractType
{

    protected function generateChoices($début, $fin)
    {

        $choices = array();

        $année = $début;
        while($année <= $fin){
            $choices[$année] = $année;
            $année = $année+1;
        }
        return $choices;

    }


    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('plainEmail', RepeatedType::class, [
                'required' => false,
                'type' => EmailType::class,
                'first_options' => [
                    'label' => 'Entrer votre nouvel email',
                ],
                'second_options' => [
                    'label' => 'Confirmer votre nouvel email',
                ],
                'invalid_message' => 'Les champs nouvels email doivent être identiques.',
                'mapped' => false,
                'attr' => [
                    'autocomplete' => 'off'
                ]
            ])
            ->add('firstname', TextType::class, [
                'required' => false,
                'label' => 'Entrer votre prénom',
            ])
            ->add('name', TextType::class, [
                'required' => false,
                'label' => 'Entrer votre nom',
            ])
            ->add('gender', ChoiceType::class, [
                'required' => true,
                'label' => 'Sexe*',
                'choices' => [
                    'Homme' => 'Homme',
                    'Femme' => 'Femme'
                ]
            ])
            ->add('age', BirthdayType::class,[
                'required' => false,
                'years' => $this->generateChoices(1910,(date("Y")-18))

            ])
            ->add('plainPassword', RepeatedType::class, [
                'required' => false,
                'type' => PasswordType::class,
                'first_options' => [
                    'label' => 'Entrer votre nouveau mot de passe',
                    'constraints' => [
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
                'label' => 'Image (JPEG/PNG)*',
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
