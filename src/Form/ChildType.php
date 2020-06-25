<?php

namespace App\Form;

use App\Entity\Allergen;
use App\Entity\Child;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChildType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('lastName', TextType::class, [
                // Le 'label' fait référence à la dénomination qui va apparaître dans ma vue
                'label' => 'NOM',
                // Le 'required' indique quant à lui que le champ est obligatoire
                'required' => true
            ])
            ->add('firstName', TextType::class, [
                'label' => 'Prénom',
                'required' => true
            ])
            ->add('birthDate', BirthdayType::class, [
                'label' => 'Date de naissance',
                // Ici, je paramètre le format d'affichage de la date de naissance
                'placeholder' => [
                    'year' => 'Year', 'month' => 'Month', 'day' => 'Day',
                ],
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'required' => true
            ])
            ->add('sex', ChoiceType::class, [
                'label' => 'Sexe',
                // 'choices' va permettre de selectionner une donnée parmis celles définies
                'choices' => [
                    'Feminin' => 'F',
                    'Masculin' => 'M',
                ],
                // La combinaison 'expanded' / 'multiple' va afficher des boutons radio sur ma vue
                // Si la valeur 'multiple' était en 'true', la vue aurait affiché des checkboxes ; Le choix aurait pu
                // être multiple alors qu'ici on ne veut que l'une ou l'autre des propositions précédemment définies
                // dans 'choices'
                'expanded' => true,
                'multiple' => false,
                'required' => true
            ])
            // 'FileType' : Fichier à uploader
            ->add('image', FileType::class, [
                'label' => 'Photo',
                // Le 'required : false' permet ici de ne pas uploader la photo à chaque mise à jour de la fiche enfant
                'required' => false,
                // Le 'mapped : false' signifie quant à lui que le champ n'est pas associé aux propriétés de l'entité
                // 'Child'
                'mapped' => false
            ])
            // Ici, je veux faire apparaître dans la fiche enfant les allergènes ; 'Allergen' est ici de type
            // 'CollectionType' puisque c'est une entité qui a plusieurs relations : Un même allergène peut toucher
            // plusieurs enfants / un enfant peut avoir plusieurs allergènes. On parle ici d''Array'.
            ->add('allergen', CollectionType::class, [
                'entry_type' => AllergenType::class,
                'required' => false
            ])
            // Une fois le 'CollectionType' défini, je peux maintenant saisir les données que je veux afficher :
            ->add('allergen', EntityType::class, [
                'label' => 'ALLERGÈNE(S)',
                'class' => Allergen::class,
                // Un enfant pouvant avoir plusieurs allergies, le choix peut être multiple
                'multiple' => true,
                'choice_label' => 'type',
                // Le 'required' est en 'false' car un enfant n'est pas obligé d'avoir des allergies
                'required' => false
            ])
            // Afin de pouvoir lier l'enfant inscrit et le compte parents, je fais appel à mon entité 'User'
            ->add('user', EntityType::class, [
                'class' => User::class,
                'label' => 'FAMILLE',
                // La selection se fera par le biais du nom de famille, dans un menu déroulant
                'choice_label' => 'lastName',
                // Tous les parents n'étant pas forcés de créer leur compte en ligne, je passe le 'required' en 'false'
                'required' => false
            ])
            // Enfin, j'ajoute un bouton de validation de mon formulaire
            ->add('submit', SubmitType::class, [
                'label' => 'Valider'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Child::class,
        ]);
    }
}
