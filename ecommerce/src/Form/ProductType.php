<?php

namespace App\Form;

use App\Entity\Platform;
use App\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'title',
                TextType::class,
                $this->getConfiguration('Titre', 'Titre du jeu')
            )
            ->add(
                'price',
                MoneyType::class,
                $this->getConfiguration('Prix', 'Prix du jeu')
            )
            ->add(
                'coverImage',
                UrlType::class,
                $this->getConfiguration('Image', 'URL de l\'image. ex: https://picsum.photos/id/47/640/480')
            )
            ->add(
                'description',
                TextareaType::class,
                $this->getConfiguration('Description', 'Description du jeu')
            )
            ->add(
                'category',
                null,
                ['label' => 'Catégorie']
            )
            ->add(
                'platforms', EntityType::class, [
                    'class' => Platform::class,
                    'label'     => 'Plateformes de jeu',
                    'expanded'  => true,
                    'multiple'  => true
                    ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
