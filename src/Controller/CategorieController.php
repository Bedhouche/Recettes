<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Form\CategorieType;
use App\Repository\CategorieRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/categorie')]
class CategorieController extends AbstractController
{
    #[Route('/', name: 'app_categorie_index', methods: ['GET'])]
    public function index(CategorieRepository $categorieRepository): Response
    {
        return $this->render('categorie/index.html.twig', [
            'categories' => $categorieRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_categorie_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $categorie = new Categorie();
        $categorie->setDateCreation(new DateTime());
        $form = $this->createForm(CategorieType::class, $categorie, [
            'exclude_nombre_recettes' => true, // Exclure le champ nombre_recettes
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $newFilename = $this->uploadImage($imageFile);
                $categorie->setImage($newFilename);
            }
            // Mise à jour du nombre de recettes
            $categorie->setNombreRecettes(count($categorie->getRecettes()));

            $entityManager->persist($categorie);
            $entityManager->flush();

            return $this->redirectToRoute('app_categorie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('categorie/new.html.twig', [
            'categorie' => $categorie,
            'form' => $form->createView(),
        ]);
    }


    #[Route('/{id}', name: 'app_categorie_show', methods: ['GET'])]
    public function show(Categorie $categorie): Response
    {
        // Récupérer les recettes associées à la catégorie
        $recettes = $categorie->getRecettes();

        return $this->render('categorie/show.html.twig', [
            'categorie' => $categorie,
            'recettes' => $recettes,
        ]);
    }

    #[Route('/{id}/recettes', name: 'app_categorie_recettes', methods: ['GET'])]
    public function showRecettes(Categorie $categorie): Response
    {
        return $this->render('categorie/recettes.html.twig', [
            'categorie' => $categorie,
            'recettes' => $categorie->getRecettes(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_categorie_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Categorie $categorie, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CategorieType::class, $categorie, [
            'exclude_nombre_recettes' => true,
        ]);
        $form->handleRequest($request);
        $categorie->setNombreRecettes(count($categorie->getRecettes()));

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $newFilename = $this->uploadImage($imageFile);
                $categorie->setImage($newFilename);
            }
            $entityManager->flush();

            return $this->redirectToRoute('app_categorie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('categorie/edit.html.twig', [
            'categorie' => $categorie,
            'form' => $form->createView(),
            'button_label' => 'Modifier', // Définir la variable button_label ici
        ]);
    }


    #[Route('/{id}', name: 'app_categorie_delete', methods: ['POST'])]
    public function delete(Request $request, Categorie $categorie, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$categorie->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($categorie);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_categorie_index', [], Response::HTTP_SEE_OTHER);
    }


    private function uploadImage(UploadedFile $file): string
    {
        $imagesDirectory = $this->getParameter('uploads_directory');

        // Générer un nom de fichier unique
        $newFilename = md5(uniqid()) . '.' . $file->guessExtension();

        try {
            // Déplacer le fichier vers le répertoire où vous souhaitez le stocker
            $file->move($imagesDirectory, $newFilename);
        } catch (FileException $e) {
            // Gérer les erreurs de téléchargement ici, si nécessaire
            throw new FileException('Une erreur s\'est produite lors de l\'enregistrement de l\'image.');
        }

        return $newFilename;
    }
}
