<?php

namespace App\Form;

use App\Entity\Movie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class MovieFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        //validation with assert on Movie entity
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
                'help' => 'Optional, if the release year is not selected movie wont show to users',

            ])
            ->add('categories', null, [
                'translation_domain' => false,
                'help' => 'Optional, choose one or more categories',

            ])
            ->add('save', SubmitType::class, [
                'attr' => ['class' => 'btn btn-success'],
                'label' => 'Create Movie',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Movie::class,
        ]);
    }
}
