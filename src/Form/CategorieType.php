<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Recette;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategorieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('description')
            ->add('image', FileType::class, [
                'label' => 'Image de la catégorie',
                'mapped' => false,
                'required' => false,
            ])
            ->add('couleur', ColorType::class, [
                'label' => 'Couleur de la catégorie',
            ])

        ->add('recettes', EntityType::class, [
        'class' => Recette::class,
        'choice_label' => 'titre', // Affiche le titre des recettes dans la liste déroulante
        'multiple' => true, // Permet de sélectionner plusieurs recettes
        'expanded' => true, // Affiche les recettes sous forme de cases à cocher
    ]);


        if (!$options['exclude_nombre_recettes']) {
            // Ajouter un champ nombre_recettes uniquement si l'option n'est pas activée
            $builder->add('nombre_recettes');
        }

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Categorie::class,
            'exclude_nombre_recettes' => false, // Par défaut, n'exclut pas le champ nombre_recettes
        ]);
    }
}
