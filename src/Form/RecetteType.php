<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Recette;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Validator\Constraints\All;

class RecetteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', null, [
                'attr' => [
                    'placeholder' => 'Titre de la recette'
                ]
            ])
            ->add('description', null, [
                'attr' => [
                    'placeholder' => 'Description de la recette'
                ]
            ])
            ->add('ingredients', TextareaType::class, [
                'label' => 'Ingrédients (Un ingrédient par ligne)',
                'attr' => [
                    'placeholder' => 'Saisissez les ingrédients ici...',
                    'help' => 'Utilisez des points pour créer une liste d\'ingrédients, par exemple : 
                    - 200g de farine
                    - 3 œufs
                    - 100g de sucre'
                ]
            ])
           /* ->add('etapes', CollectionType::class, [
                'label' => 'Étapes de préparation',
                'entry_type' => TextareaType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'prototype_name' => '__etapes_index__',
                'attr' => [
                    'class' => 'etapes-collection',
                    'placeholder' => 'Saisissez les étapes ici...'
                ],
            ])*/
            ->add('contenu', FileType::class, [
                'label' => 'Contenu (image ou vidéo)',
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => 'Sélectionnez une image ou une vidéo',
                    'accept' => 'image/*,video/*', // Seulement les images et vidéos
                ],
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/*', // Accepter uniquement les fichiers d'image
                            'video/*', // Accepter uniquement les fichiers vidéo
                        ],
                        'mimeTypesMessage' => 'Please upload a valid image or video file',
                    ])
                ]
            ])
            ->add('temp_prep', null, [
                'attr' => [
                    'placeholder' => 'Durée de préparation (en minutes)'
                ]
            ])
            ->add('temp_cuisson', null, [
                'attr' => [
                    'placeholder' => 'Durée de cuisson (en minutes)'
                ]
            ])
            ->add('difficulte', ChoiceType::class, [
                'label' => 'Difficulté',
                'choices' => [
                    'Facile' => 1,
                    'Moyenne' => 2,
                    'Difficile' => 3,
                ],
            ])
            ->add('categories', EntityType::class, [
                'class' => Categorie::class,
                'query_builder' => function (EntityRepository $er) use ($options) {
                    return $er->createQueryBuilder('c')
                        ->orderBy('c.nom', 'ASC');
                },
                'choice_label' => 'nom',
                'placeholder' => 'Sélectionnez une ou plusieurs catégories',
                'multiple' => true, // Permettre la sélection multiple
                'expanded' => false, // Afficher les options sous forme de liste déroulante
                'required' => false,
            ]);
    }
        public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recette::class,
            'search' => null, // Ajoutez une option de recherche par défaut
        ]);
    }
}
