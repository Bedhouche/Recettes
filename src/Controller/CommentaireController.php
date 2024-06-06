<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Entity\Recette;
use App\Form\CommentaireType;
use App\Repository\CommentaireRepository;
use DateTime; // Ajout de l'importation de DateTime
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\SecurityBundle\Security;
/**
 * @method getDoctrine()
 */
#[Route('recette/commentaire')]
class CommentaireController extends AbstractController
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    #[Route('/', name: 'app_commentaire_index', methods: ['GET'])]
    public function index(CommentaireRepository $commentaireRepository, $recetteId = null): Response
    {
        $recette = null;
        $commentaires = null;

        // Si un $recetteId est fourni, récupérer la recette correspondante
        if ($recetteId) {
            $recette = $this->getDoctrine()->getRepository(Recette::class)->find($recetteId);
            if (!$recette) {
                throw $this->createNotFoundException('La recette n\'existe pas');
            }
            // Récupérer les commentaires spécifiques à cette recette
            $commentaires = $commentaireRepository->findBy(['recette' => $recette]);
        } else {
            // Sinon, récupérer tous les commentaires
            $commentaires = $commentaireRepository->findAll();
        }

        return $this->render('commentaire/index.html.twig', [
            'commentaires' => $commentaires,
            'recette' => $recette,
        ]);
    }


    #[Route('/new/{recetteId}', name: 'app_commentaire_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, $recetteId): Response
    {
        if (!$this->security->isGranted('IS_AUTHENTICATED_FULLY')) {
            // Rediriger vers la page de connexion si l'utilisateur n'est pas connecté
            return $this->redirectToRoute('app_login');
        }

        // Récupérer la recette associée à l'ID
        $recette = $entityManager->getRepository(Recette::class)->find($recetteId);

        // Vérifier si la recette existe
        if (!$recette) {
            throw $this->createNotFoundException('La recette n\'existe pas');
        }

        $commentaire = new Commentaire();
        $commentaire->setRecette($recette);

        // Récupérer l'utilisateur connecté
        $user = $this->getUser();
        $commentaire->setUsers($user);

        $form = $this->createForm(CommentaireType::class, $commentaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Ajouter la date de création actuelle
            $commentaire->setDateCreation(new DateTime());

            $entityManager->persist($commentaire);
            $entityManager->flush();


            return $this->redirectToRoute('app_recette_commentaires', ['id' => $recette->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('commentaire/new.html.twig', [
            'commentaire' => $commentaire,
            'form' => $form,
            'recette' => $recette,

        ]);
    }

    #[Route('/{id}', name: 'app_commentaire_show', methods: ['GET'])]
    public function show(Commentaire $commentaire): Response
    {
        return $this->render('commentaire/show.html.twig', [
            'commentaire' => $commentaire,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_commentaire_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Commentaire $commentaire, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CommentaireType::class, $commentaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            // Rediriger vers la page des commentaires de la recette associée
            return $this->redirectToRoute('app_recette_commentaires', ['id' => $commentaire->getRecette()->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('commentaire/edit.html.twig', [
            'commentaire' => $commentaire,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_commentaire_delete', methods: ['POST'])]
    public function delete(Request $request, Commentaire $commentaire, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$commentaire->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($commentaire);
            $entityManager->flush();
        }

        // Rediriger vers la page des commentaires de la recette associée
        return $this->redirectToRoute('app_recette_commentaires', ['id' => $commentaire->getRecette()->getId()], Response::HTTP_SEE_OTHER);
    }

}
