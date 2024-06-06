<?php

namespace App\Form;

use App\Entity\Commentaire;
use App\Entity\Recette;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentaireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('contenu', TextareaType::class, [
                'label' => 'Votre commentaire'
            ])

            /*->add('recette', EntityType::class, [
                'class' => Recette::class,
                'choice_label' => 'titre', // Champ utilisé pour l'affichage des options
                'placeholder' => 'Sélectionner une recette', // Texte par défaut
            ]);*/
            // Vous n'avez pas besoin de permettre à l'utilisateur de choisir la recette,
            // car le commentaire sera toujours lié à une recette spécifique.
            // Supprimez donc ce champ de votre formulaire.
            // ->add('recette', EntityType::class, [
            //     'class' => Recette::class,
            //     'choice_label' => 'id',
            // ])
            // De même, vous n'avez pas besoin de permettre à l'utilisateur de choisir l'utilisateur,
            // car le commentaire sera toujours lié à l'utilisateur connecté.
            // Supprimez donc ce champ de votre formulaire.
            // ->add('users', EntityType::class, [
            //     'class' => User::class,
            //     'choice_label' => 'id',
            // ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commentaire::class,
        ]);
    }
}
