<?php

namespace App\Controller\Backend;

use App\Entity\User;
use App\Entity\UserInfos;
use App\Form\ProfileType;
use App\Form\EditUserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

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
            'user' => $user,
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

    #[Route('/profile/{id}/edit-user', name: 'app.profile.edit.user', methods: ['GET', 'POST'])]
    public function editUser(Request $request, User $user, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): Response
    {
        $form = $this->createForm(EditUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hashedPassword = $passwordHasher->hashPassword($user, $form->get('password')->getData());
            $user->setPassword($hashedPassword);

            $em->flush();

            $this->addFlash('success', 'Informations de connexion modifié avec succès.');
            return $this->redirectToRoute('app.profile.index');
        }

        return $this->render('Backend/Profile/editUser.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/profile/{id}/delete', name: 'app.profile.delete', methods: ['GET', 'POST'])]
    public function deleteUser(User $user, Request $request, EntityManagerInterface $em, Security $security): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            // Supprimez les entités liées manuellement
            if ($user->getUserInfos()) {
                $em->remove($user->getUserInfos());
            }
            if ($user->getPayment()) {
                $em->remove($user->getPayment());
            }
            foreach ($user->getAnnonces() as $annonce) {
                $em->remove($annonce);
            }

            $em->remove($user);
            $em->flush();

            $security->logout(false);
            $this->addFlash('success', 'Votre compte est supprimé.');
        }
        return $this->redirectToRoute('app.home');
    }
}







