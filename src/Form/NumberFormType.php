<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Regex;

class NumberFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('countryCode', ChoiceType::class, [
                'label' => false,
                'choices' => ["France" => '33'],
                'row_attr' => ['class' => 'w-100'],
                'attr' => [
                    'class' => 'form-control col-12',
                ],
                'constraints' => [
                    new Choice(['choices' => ["33"]]),
                ]
            ])
            ->add('number', TextType::class, [
                'label' => false,
                'row_attr' => ['class' => 'w-100'],
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'number',
                    'pattern' => '^0[1-9]{1}[0-9]{8}$',
                ],
                'constraints' => [
                    new Regex('/^0[1-9]{1}[0-9]{8}$/')
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
