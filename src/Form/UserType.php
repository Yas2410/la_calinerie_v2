<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
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
            ->add('address', textType::class, [
                'label' => 'Adresse'
            ])
            ->add('address2', textType::class, [
                'label' => 'Adresse Secondaire'
            ])
            ->add('city', textType::class, [
                'label' => 'Ville'
            ])
            ->add('zipcode', IntegerType::class, [
                'label' => 'Code Postal'
            ])
            ->add('phoneNumber', textType::class, [
                'label' => 'Téléphone 1'
            ])
            ->add('phoneNumber2', textType::class, [
                'label' => 'Téléphone 2'
            ])
            ->add('email', textType::class, [
                'label' => 'Mail'
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Valider'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
