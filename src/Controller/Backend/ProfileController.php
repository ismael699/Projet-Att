<?php

namespace App\Controller\Backend;

use App\Entity\UserInfos;
use App\Form\ProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app.profile.index')]
    public function index(): Response
    {
        /**
        * @var User $user
        */
        $user = $this->getUser(); // récupère l'utilisateur connecté 
        $userInfos = $user->getUserInfos(); // récupère les informations de l'utilisateur

        return $this->render('Backend/Profile/index.html.twig', [
            'userInfos' => $userInfos,
        ]);
    }

    #[Route('/profile/create', name: 'app.profile.create', methods: ['GET', 'POST'])]
    public function createProfile(Request $request, EntityManagerInterface $em): Response
    {
        $userInfos = new UserInfos(); // nouveau profile

        $form = $this->createForm(ProfileType::class, $userInfos);
        $form->handleRequest($request); //  traite la requête et met à jour l'entité avec les données soumises du formulaire

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser(); // lie les infos à l'utilisateur connecté
            $userInfos->setUser($user);

            $em->persist($userInfos);
            $em->flush();

            $this->addFlash('success', 'Profile enregistrées avec succès.');
            return $this->redirectToRoute('app.profile.index'); 
        }

        return $this->render('Backend/Profile/create.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/profile/{id}/edit', name: 'app.profile.edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, UserInfos $userInfos, EntityManagerInterface $em): Response 
    {
        $form = $this->createForm(ProfileType::class, $userInfos);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Profile modifié avec succès.');
            return $this->redirectToRoute('app.profile.index');
        }

        return $this->render('Backend/Profile/edit.html.twig', [
            'userInfos' => $userInfos,
            'form' => $form,
        ]);
    }
}
