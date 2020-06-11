<?php

namespace App\Form;

use App\Entity\Child;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsFalse;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints\TypeValidator;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('lastName', TextType::class, [
                /* Le 'Label' fait référence au terme qui va être affiché sur ma vue, au dessus de l'input*/
                'label' => 'NOM'
            ])
            ->add('firstName', TextType::class, [
                'label' => 'Prénom'
            ])
            ->add('address', TextType::class, [
                'label' => 'Adresse'
            ])
            ->add('address2', TextType::class, [
                'label' => 'Adresse 2 (facultatif)',
                /* Ici, le 'required => false' permet de valider le formulaire d'inscription sans
                être dans l'obligation d'ajouter un deuxième numéro de téléphone*/
                'required' => false
            ])
            ->add('zipcode', IntegerType::class, [
                'label' => 'Code Postal',
                'constraints' => [
                    new Range([
                        'min' => 5,
                        'max' => 5,
                        'notInRangeMessage' => 'Veuillez saisir un code postal valide à 5 chiffres',
                    ])
                ]
            ])
            ->add('city', TextType::class, [
        'label' => 'Ville'
    ])
        ->add('phoneNumber', TextType::class, [
            'label' => 'Téléphone',
            /* Ici, j'applique une contrainte afin que l'utilisateur qui s'enregistre renseigne un numéro
            de téléphone à 10 chiffres*/
            'constraints' => [
                new Range([
                    'min' => 10,
                    'max' => 10,
                    'notInRangeMessage' => 'Veuillez saisir un numéro de téléphone à 10 chiffres',
                ])
            ]
        ])
        ->add('phoneNumber2', TextType::class, [
            'label' => 'Téléphone 2 (facultatif) ',
            'required' => false
        ])
        ->add('email', TextType::class, [
            'label' => 'Mail'
            /* CF USER ENTITY
            * @UniqueEntity(fields={"email"}, message="Cette adresse mail est déjà utilisée")*/
        ])
        ->add('plainPassword', RepeatedType::class, [
            // instead of being set onto the object directly,
            // this is read and encoded in the controller
            'type' => PasswordType::class,
            'label' => 'Mot de Passe',
            /* Ici, dans la mesure où c'est un mot de passe avec 'Validation du mot de passe', j'applique un
            message d'erreur si l'utilisateur entre un mot de passe différent dans le champ
             'valider le mot de passe'*/
            'invalid_message' => 'Le mot de passe n\'est pas identique',
            'options' => ['attr' => ['class' => 'password-field']],
            'required' => true,
            'first_options' => ['label' => 'Mot de Passe'],
            'second_options' => ['label' => 'Confirmer le mot de passe'],
            'mapped' => false,
            /* Au niveau des contraintes, l'utilisateur va obligatoirement devoir saisir un mdp ('NotBlank') ;
            Ce dernier devra être compris entre 8 et 25 caractères ('Length'). Dans le cas contraire, les
            messages d'erreurs listés ci-dessous apparaitront en cas de saisie incorrecte*/
            'constraints' => [
                new NotBlank([
                    'message' => 'Veuillez saisir un mot de passe',
                ]),
                new Length([
                    'min' => 8,
                    'minMessage' => 'Votre mot de passe doit contenir au minimum {{ limit }} caractères',
                    // max length allowed by Symfony for security reasons
                    'max' => 25,
                    'maxMessage' => 'Votre mot de passe doit contenir au maximum {{ limit }} caractères'
                ]),
            ],
        ])
        ->add('agreeTerms', CheckboxType::class, [
            'label' => "J'accepte les conditions générales du site",
            'mapped' => false,
            'constraints' => [
                new IsTrue([
                    'message' => 'Veuillez cocher cette case pour continuer',
                ]),
            ],
        ])
        ->add('submit', SubmitType::class, [
            'label' => 'Je m\'inscris !'
        ]);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class, Child::class

        ]);
    }
}
