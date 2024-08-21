<?php

namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                ]
            ])
            ->add('to', ChoiceType::class, [
                'choices' => [
                    'admin' => 'admin@recettes.com',
                    'comptabilité' => 'comptabilite@recettes.com',
                    'commercial' => 'commercial@recettes.com',
                    'technique' => 'technique@recettes.com',
                ],
                'constraints' => [
                    new NotBlank(),
                ]
            ])
            ->add('email', TextType::class, [
                'constraints' => [
                    new Email(),
                    new NotBlank(),
                ]
            ])
            ->add('message', TextareaType::class, [
                'attr' => ['rows' => 6],
                'constraints' => [
                    new NotBlank(),
                ]
            ])
            ->add(
                'submit',
                SubmitType::class,
                ['attr' => ['class' => 'btn btn-success']]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
