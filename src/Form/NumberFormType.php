<?php

namespace App\Form;

use Misd\PhoneNumberBundle\Form\Type\PhoneNumberType;
use Misd\PhoneNumberBundle\Validator\Constraints\PhoneNumber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NumberFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('phoneNumber', PhoneNumberType::class, [
                'widget' => PhoneNumberType::WIDGET_COUNTRY_CHOICE,
                'label' => 'phone_number',
                'country_options' => [
                  'attr' => [
                      'class' => 'form-control',
                  ]
                ],
                'number_options' => [
                    'attr' => [
                        'class' => 'form-control mt-3',
                    ]
                ],
                'constraints' => [
                    new PhoneNumber(),
                ]
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
