<?php

namespace App\DataFixtures;

use App\Entity\Categorie;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\AsciiSlugger;

class AppFixtures extends Fixture
{public function load(ObjectManager $manager)
{
    $categoriesData = [
        ['nom' => 'Catégorie 1', 'couleur' => '#FF5733', 'description' => 'Description de la catégorie 1'],
        ['nom' => 'Catégorie 2', 'couleur' => '#33FF68', 'description' => 'Description de la catégorie 2'],
        ['nom' => 'Catégorie 3', 'couleur' => '#339CFF', 'description' => 'Description de la catégorie 3'],
        ['nom' => 'Catégorie 4', 'couleur' => '#FF33E1', 'description' => 'Description de la catégorie 4'],
        ['nom' => 'Catégorie 5', 'couleur' => '#AA33FF', 'description' => 'Description de la catégorie 5'],
        ['nom' => 'Catégorie 6', 'couleur' => '#33FFFF', 'description' => 'Description de la catégorie 6'],
        ['nom' => 'Autre', 'couleur' => '#CCCCCC', 'description' => 'Description de la catégorie Autre'],
    ];


    $imagesDirectory = 'uploads/';
    $slugger = new AsciiSlugger();

    foreach ($categoriesData as $key => $data) {
        $categorie = new Categorie();
        $categorie->setNom($data['nom']);
        $categorie->setCouleur($data['couleur']);
        $categorie->setDescription($data['description']);
        $categorie->setDateCreation(new DateTime()); // Utiliser la date actuelle

        // Vérifiez si le fichier d'image existe
        $imagePath = 'public/uploads/' . ($key + 1) . '.jpg'; // Générer dynamiquement le chemin de l'image
        if (file_exists($imagePath)) {
            $imageName = $slugger->slug(pathinfo($imagePath, PATHINFO_FILENAME)) . '.' . pathinfo($imagePath, PATHINFO_EXTENSION);
            // Copiez l'image vers le répertoire d'images
            copy($imagePath, 'public/uploads/' . $imageName);
            $categorie->setImage($imageName);
        }


        $manager->persist($categorie);
    }

    $manager->flush();
}
}
