<?php

namespace App\Form;

use App\Entity\Movie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class MovieFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', null, [
                'translation_domain' => false,
                'help' => 'Mandatory',

            ])
            ->add('storyline', null, [
                'translation_domain' => false,
                'help' => 'Optional',

            ])
            ->add('releaseYear', null, [
                'translation_domain' => false,
                'years' => range(date('1901'), date('Y') + 10),
                'help' => 'Optional',

            ])
            ->add('categories', null, [
                'translation_domain' => false,
                'help' => 'Optional, choose one or more categories',

            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Movie::class,
        ]);
    }
}
