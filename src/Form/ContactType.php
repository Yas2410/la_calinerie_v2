<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('civility', ChoiceType::class, [
                'label' => false,
                'choices' => [
                    'M.' => 'M.',
                    'Mme' => 'Mme',
                ],
                'expanded' => true
            ])
            ->add('lastName', TextType::class, [
                'label' => 'NOM : '
            ])
            ->add('firstName', TextType::class, [
                'label' => 'Prénom : '
            ])
            ->add('email', EmailType::class, [
                'label' => 'Mail : '
            ])
            ->add('object', ChoiceType::class, [
                'label' => 'Votre message concerne',
                'choices' => [
                    'La crèche' => 'La crèche',
                    'Une Inscription' => 'Une Inscription',
                    'Un Problème technique' => 'Un Problème technique',
                    'Mon Compte' => 'Mon Compte',
                    'Autre' => 'Autre'

                ]
            ])
            ->add('message', TextareaType::class, [
                'label' => "Votre Message"
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Envoyer'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
