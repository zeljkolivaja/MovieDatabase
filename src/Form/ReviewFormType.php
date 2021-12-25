<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ReviewFormType extends AbstractType
{


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('reviewTitle', null, [
                'translation_domain' => false,
                'label' => 'Add title for your review.',
                'constraints' => [
                    new NotBlank(),
                    new Length(['min' => 2, 'max' => 35]),
                ],
            ])
            ->add('review', TextareaType::class, [
                'translation_domain' => false,
                'label' => 'Enter your review.',
                'constraints' => [
                    new NotBlank(),
                    new Length(['min' => 40, 'max' => 4000]),
                ],
            ]);
    }
}
