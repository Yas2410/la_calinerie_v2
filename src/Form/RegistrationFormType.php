<?php

namespace App\Form;

use App\Entity\Child;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;


class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('lastName', TextType::class, [
                'label' => 'NOM'
            ])
            ->add('firstName', TextType::class, [
                'label' => 'Prénom'
            ])
            ->add('address', TextType::class, [
                'label' => 'Adresse'
            ])
            ->add('address2', TextType::class, [
                'label' => 'Adresse 2',
                'required' => false
            ])
            ->add('zipcode', IntegerType::class, [

                'label' => 'Code Postal'
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville'
            ])
            ->add('phoneNumber', TextType::class, [
                'label' => 'Téléphone'
            ])
            ->add('phoneNumber2', TextType::class, [
                'label' => 'Téléphone 2',
                'required' => false
            ])
            ->add('email', TextType::class, [
                'label' => 'Mail'
            ])
            ->add('plainPassword', RepeatedType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'type' => PasswordType::class,
                'invalid_message' => 'Le mot de passe n\'est pas identique',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options'  => ['label' => 'Mot de Passe'],
                'second_options' => ['label' => 'Confirmer le mot de passe'],
                'mapped' => false,
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
            ->add('agreeTerms', CheckboxType::class, [
            'label' => "J'accepte les conditions générales du site",
            'mapped' => false,
            'constraints' => [
                new IsTrue([
                    'message' => 'You should agree to our terms.',
                ]),
            ],
        ])
            ->add('Submit', SubmitType::class)
;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class, Child::class

        ]);
    }
}
