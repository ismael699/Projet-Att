<?php

namespace App\Controller\Backend;

use App\Entity\User;
use App\Entity\Annonce;
use App\Form\AnnonceType;
use App\Form\AnnonceSearchType;
use App\Repository\AnnonceRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ConversationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class AnnonceController extends AbstractController
{
    #[Route('/annonce', name: 'app.annonce.index', methods: ['GET', 'POST'])]
    public function index(Request $request, AnnonceRepository $annoncesRepo, ConversationRepository $conversationRepo): Response
    {   
        $form = $this->createForm(AnnonceSearchType::class); // génération du formulaire de recherche
        $form->handleRequest($request);
        $annonces = []; 
        // Vérification si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            // Récupération des annonces filtrées en fonction des données du formulaire
            $annonces = $annoncesRepo->findBySearchCriteria($data);
        } else {
            // Récupération de toutes les annonces si le formulaire n'est pas soumis
            $annonces = $annoncesRepo->findAllOrderedByCreatedAt();
        }

        $user = $this->getUser(); // Récupération de l'utilisateur connecté
        if ($user) { // Vérification si l'utilisateur est connecté
            // Récupération des conversations de l'utilisateur connecté
            $conversations = $conversationRepo->findBy(['creator' => $user]);  // Ajuste le champ 'creator' selon ta logique
        } else {
            $conversations = [];  // Pas d'utilisateur connecté, pas de conversations
        }

        return $this->render('Backend/Annonce/index.html.twig', [
            'form' => $form->createView(),
            'annonces' => $annonces,
            'conversations' => $conversations,
        ]);
    }

    #[Route('/annonce/create', name: 'app.annonce.create', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $em, TokenStorageInterface $tokenStorage,): Response
    {
        $user = $tokenStorage->getToken()->getUser(); // récupère l'utilisateur connecté 

        // vérifie que l'utilisateur a le rôle ROLE_ADMIN ou ROLE_CHAUFFEUR
        if (!$user instanceof User || !array_intersect($user->getRoles(), ['ROLE_ADMIN', 'ROLE_CHAUFFEUR'])) {
            $this->addFlash('error', 'Vous n\'avez pas les autorisations nécessaires pour créer une annonce.');
            return $this->redirectToRoute('app.annonce.index');
        }

        // vérifie si l'utilisateur a rempli son profil et s'il à le ['ROLE_CHAUFFEUR'] et ['ROLE_ADMIN']
        if (array_intersect(['ROLE_CHAUFFEUR', 'ROLE_ADMIN'], $user->getRoles()) && (!$user->getUserInfos() || 
            !$user->getUserInfos()->getPhoneNumber())) 
            {

            $this->addFlash('error', 'Vous devez remplir votre profil avant de pouvoir créer une annonce.');
            return $this->redirectToRoute('app.profile.create');
        }

        $annonce = new Annonce();
        $annonce->setUser($user); // relie le chauffeur à l'annonce 

        $form = $this->createForm(AnnonceType::class, $annonce);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($annonce);
            $em->flush();

            $this->addFlash('success', 'Annonce créer avec succès.');
            return $this->redirectToRoute('app.annonce.index');
        }

        return $this->render('Backend/Annonce/create.html.twig', [
            'form' => $form,
        ]);
    }
}
