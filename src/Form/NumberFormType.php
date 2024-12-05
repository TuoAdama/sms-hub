<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NumberFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('country', ChoiceType::class, [
                'label' => false,
                'choices' => ["France" => 'france'],
                'row_attr' => ['class' => 'w-100'],
                'attr' => [
                    'class' => 'form-control col-12',
                ]
            ])
            ->add('number', NumberType::class, [
                'label' => false,
                'row_attr' => ['class' => 'w-100'],
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'number',
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
