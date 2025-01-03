<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => false,
                'required' => true,
                'row_attr' => [
                    'class' => 'd-flex w-100'
                ],
                'attr' => [
                    'class' => 'form-control',
                    "placeholder" => "Email"
                ]
            ])
            ->add('name', TextType::class, [
                'label' => false,
                'required' => true,
                'row_attr' => [
                    'class' => 'd-flex w-100'
                ],
                'attr' => [
                    'class' => 'form-control',
                    "placeholder" => "form.lastname"
                ]
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'required' => true,
                'mapped' => false,
                'attr' => [
                    'autocomplete' => 'new-password',
                    'class' => 'form-control',
                    "placeholder" => "password"
                ],
                'first_options'  => [
                    'label' => false,
                    'row_attr' => [
                        'class' => 'd-flex w-100'
                    ],
                    'attr' => [
                        'class' => 'form-control',
                        "placeholder" => "password"
                    ]
                ],
                'second_options' => [
                    'label' => false,
                    'row_attr' => [
                        'class' => 'd-flex w-100'
                    ],
                    'attr' => [
                        'class' => 'form-control',
                        "placeholder" => "c_password"
                    ]
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
