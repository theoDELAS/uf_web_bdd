<?php

namespace App\Form;

use App\Entity\Historical;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HistoricalType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('mail', TextType::class,
                $this->getConfiguration('Adresse mail' ,"Adresse où vous sera envoyé la facture et les codes", [
                    'attr' => [
                        'class' => "w-75"
                    ]
                ]))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Historical::class,
        ]);
    }
}
