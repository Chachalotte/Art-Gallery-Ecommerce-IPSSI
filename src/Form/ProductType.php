<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class,[
                'label' => 'Nom du produit'
            ])
            ->add('description', TextType::class)
            ->add('img', FileType::class, [
                'label' => 'Image (JPEG/PNG)',
                'required' => false,
                'data_class' => null,
                'attr' => [
                    'class' => 'blabla'
                ]
            ])
            ->add('price', NumberType::class,[
                'label' => 'Prix'
            ])
            ->add('color', TextType::class,[
                'label' => 'Couleur'
            ])
            // ->add('orderItem')
            // ->add('artist')
            ->add('category', EntityType::class, [
                'label' => 'Catégorie',
                'class' => Category::class,
                'multiple' => false,
                'expanded' => false,
                'choice_label' => 'name'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
