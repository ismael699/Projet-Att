<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\AdminUserType;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Repository\UserInfosRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/admin/user', name: 'admin.user', methods: ['GET'])]
class UserController extends AbstractController
{
    #[Route('/', name: '.index')]
    public function index(UserRepository $userRepo, UserInfosRepository $userInfosRepo): Response
    {
        return $this->render('Admin/User/index.html.twig', [
            'users' => $userRepo->findAllOrderedByCreatedAt(),
            'userInfos' => $userInfosRepo->findAll(),
        ]);
    }

    #[Route('/create', name: '.create', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User();

        $form = $this->createForm(AdminUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hashedPassword = $passwordHasher->hashPassword($user, $form->get('password')->getData());
            $user->setPassword($hashedPassword);

            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Utilisateur créer avec succès.');
            return $this->redirectToRoute('admin.user.index');
        }

        return $this->render('Admin/User/create.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: '.edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $em): Response 
    {
        $form = $this->createForm(AdminUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Utilisateur modifié avec succès.');
            return $this->redirectToRoute('admin.user.index');
        }

        return $this->render('Admin/User/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: '.delete', methods: ['POST'])]
    public function delete(User $user, Request $request, EntityManagerInterface $em): Response 
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $em->remove($user);
            $em->flush();

            $this->addFlash('success', 'Utilisateur supprimé avec succès.');
        } else {
            $this->addFlash('error', 'Erreur lors de la suppression de l\'utilisateur.');
        }

        return $this->redirectToRoute('admin.user.index');
    }
}
