<?php

namespace App\Form;

use App\Entity\Search;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'search',
                TextType::class,
                $this->getConfiguration('Titre', 'Nom du jeu à rechercher', [
                    'attr' => [
                        'aria-describedby' => "button-addon2"
                    ]
                ]))
            ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Search::class,
        ]);
    }
}
