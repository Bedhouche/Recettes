<?php

namespace App\Controller;
use App\Entity\User;
use App\Entity\Favori;
use App\Entity\Recette;
use App\Form\RecetteType;
use App\Repository\RecetteRepository;
use DateTime;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Mime\MimeTypes;
use Symfony\Component\Security\Core\User\UserInterface;


/**
 * @method getDoctrine()
 */
#[Route('/recette')]
class RecetteController extends AbstractController
{
    #[Route('/', name: 'app_recette_index', methods: ['GET'])]
    public function index(RecetteRepository $recetteRepository): Response
    {

        return $this->render('recette/index.html.twig', [
            'recettes' => $recetteRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_recette_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            // Rediriger vers la page de connexion si l'utilisateur n'est pas connecté
            return $this->redirectToRoute('app_login');
        }

        $recette = new Recette();
        $form = $this->createForm(RecetteType::class, $recette);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Associer l'utilisateur connecté à la recette
            $recette->setUser($user);
            $recette->setDateCreation(new DateTime());

            // Récupération du fichier envoyé
            $file = $form->get('contenu')->getData();

            // Vérifier s'il y a un fichier envoyé
            if ($file instanceof UploadedFile) {
                // Gérer le téléchargement du fichier
                $fileName = $this->uploadContenu($file);
                // Mettre à jour le contenu de la recette avec le nom du fichier
                $recette->setContenu($fileName);
            }

            $entityManager->persist($recette);
            $entityManager->flush();

            return $this->redirectToRoute('app_recette_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('recette/new.html.twig', [
            'recette' => $recette,
            'form' => $form,
        ]);
    }

    /*
        #[Route('/{id}', name: 'app_recette_show', methods: ['GET'])]
        public function show(Recette $recette): Response
        {
            return $this->render('recette/show.html.twig', [
                'recette' => $recette,
            ]);
        }
    */

    #[Route('/{id}', name: 'app_recette_show', methods: ['GET'])]
    public function show(Recette $recette): Response
    {
        // Traitement du contenu de la recette
        $contenu = null;
        if ($recette->getContenu()) {
            $filename = $recette->getContenu();
            $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif','webp'])) {
                // Si c'est une image
                $contenu = '<img src="' . $this->getParameter('uploads_directory') . '/' . $filename . '" alt="Image">';
            } elseif (in_array($extension, ['mp4', 'avi', 'mov'])) {
                // Si c'est une vidéo
                $contenu = '<video controls>';
                $contenu .= '<source src="' . $this->getParameter('uploads_directory') . '/' . $filename . '" type="video/mp4">';
                $contenu .= 'Your browser does not support the video tag.';
                $contenu .= '</video>';
            }
        }

        return $this->render('recette/show.html.twig', [
            'recette' => $recette,
            'contenu' => $contenu,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_recette_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Recette $recette, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RecetteType::class, $recette);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupération du fichier envoyé
            $file = $form->get('contenu')->getData();

            // Vérifier s'il y a un fichier envoyé
            if ($file instanceof UploadedFile) { // Vérifie si le fichier est une instance de UploadedFile
                // Gérer le téléchargement du fichier
                $fileName = $this->uploadContenu($file);

                // Mettre à jour le contenu de la recette avec le nom du fichier
                $recette->setContenu($fileName);
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_recette_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('recette/edit.html.twig', [
            'recette' => $recette,
            'form' => $form,
        ]);
    }
/*
    #[Route('/{id}/edit', name: 'app_recette_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Recette $recette, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RecetteType::class, $recette);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_recette_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('recette/edit.html.twig', [
            'recette' => $recette,
            'form' => $form,
        ]);
    }*/

    #[Route('/{id}', name: 'app_recette_delete', methods: ['POST'])]
    public function delete(Request $request, Recette $recette, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$recette->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($recette);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_recette_index', [], Response::HTTP_SEE_OTHER);
    }

    private function uploadContenu(UploadedFile $file): string
    {
        $uploadsDirectory = $this->getParameter('uploads_directory');

        // Générer un nom de fichier unique
        $fileName = md5(uniqid()) . '.' . $file->guessExtension();

        try {
            // Déplacer le fichier vers le répertoire où vous souhaitez le stocker
            $file->move($uploadsDirectory, $fileName);
        } catch (FileException $e) {
            // Gérer les erreurs de téléchargement ici, si nécessaire
            throw new FileException('Une erreur s\'est produite lors de l\'enregistrement du fichier.');
        }

        // Utiliser MimeTypeGuesser pour obtenir le type MIME
        $guesser = MimeTypes::getDefault();
        $mimeType = $guesser->guessMimeType($uploadsDirectory . '/' . $fileName);

        if ($mimeType === null) {
            throw new FileException('Impossible de récupérer le type MIME du fichier.');
        }

        // Vous pouvez faire d'autres traitements ici si nécessaire

        return $fileName;

    }

    #[Route('/{id}/commentaires', name: 'app_recette_commentaires', methods: ['GET'])]
    public function commentaires(RecetteRepository $recetteRepository, $id): Response
    {
        // Récupérer la recette à partir de son ID
        $recette = $recetteRepository->find($id);
        if (!$recette) {
            throw $this->createNotFoundException('La recette n\'existe pas');
        }

        // Récupérer les commentaires associés à cette recette
        $commentaires = $recette->getCommentaires();

        return $this->render('recette/commentaires.html.twig', [
            'recette' => $recette,
            'commentaires' => $commentaires,
        ]);
    }




    #[Route('/{id}/like', name: 'app_recette_like', methods: ['POST'])]
    public function likeRecipe(Request $request, Recette $recette, EntityManagerInterface $entityManager): Response
    {
        // Obtenez l'utilisateur actuellement connecté
        $user = $this->getUser();

        // Assurez-vous que l'utilisateur est connecté et qu'il s'agit bien d'un objet User
        if ($user instanceof User) {
            // Recherchez si l'utilisateur a déjà aimé cette recette
            $favori = $recette->getFavoris()->filter(function ($favori) use ($user) {
                return $favori->getUsers()->contains($user);
            })->first();

            if ($favori) {
                // L'utilisateur a déjà aimé cette recette, supprimez son "like"
                $entityManager->remove($favori);
            } else {
                // L'utilisateur n'a pas encore aimé cette recette, ajoutez son "like"
                $favori = new Favori();
                $favori->setRecette($recette);
                $favori->addUser($user);
                $entityManager->persist($favori);
            }

            $entityManager->flush();
        } else {
            // Gérer le cas où l'utilisateur n'est pas connecté ou n'est pas un objet User
            return $this->redirectToRoute('app_login');
        }

        // Redirigez l'utilisateur vers la page de la recette ou une autre page appropriée
        return $this->redirectToRoute('app_recette_index');
    }
}