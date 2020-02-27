<?php

namespace App\Form;

use App\Entity\Product;
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
                $this->getConfiguration('Image', 'Adresse de l\'image')
            )
            ->add(
                'description',
                TextareaType::class,
                $this->getConfiguration('Description', 'Description sur la qualité de votre jeu')
            )
            ->add(
                'category',
                null,
                ['label' => 'Catégorie']
            )
            ->add(
                'images',
                CollectionType::class,
                [
                    'entry_type' => ImageType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
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
