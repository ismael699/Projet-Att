<?php

namespace App\Controller\Admin;

use App\Entity\Annonce;
use App\Form\AnnonceType;
use App\Repository\AnnonceRepository;
use App\Repository\UserInfosRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/annonce', name: 'admin.annonce', methods: ['GET'])]
class AnnonceController extends AbstractController
{
    #[Route('/', name: '.index')]
    public function index(AnnonceRepository $annonceRepo): Response
    {
        return $this->render('Admin/Annonce/index.html.twig', [
            'annonces' => $annonceRepo->findAll(),
        ]);
    }

    #[Route('/create', name: '.create', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $em): Response 
    {
        $annonce = new Annonce();

        $form = $this->createForm(AnnonceType::class, $annonce);
        $form->handleRequest($request); //  traite la requête et met à jour l'entité avec les données soumises du formulaire

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($annonce);
            $em->flush();

            $this->addFlash('success', 'Annonce créer avec succès.');

            return $this->redirectToRoute('admin.annonce.index');
        }

        return $this->render('Admin/Annonce/create.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/{id}/edit', name: '.edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Annonce $annonce, EntityManagerInterface $em): Response 
    {
        $form = $this->createForm(AnnonceType::class, $annonce);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Annonce modifié avec succès.');
            return $this->redirectToRoute('admin.annonce.index');
        }

        return $this->render('Admin/Annonce/edit.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: '.delete', methods: ['POST'])]
    public function delete(Annonce $annonce, Request $request, EntityManagerInterface $em): Response 
    {
        if ($this->isCsrfTokenValid('delete' . $annonce->getId(), $request->request->get('_token'))) {
            $em->remove($annonce);
            $em->flush();

            $this->addFlash('success', 'Annonce supprimé avec succès.');
        } else {
            $this->addFlash('error', 'Erreur lors de la suppression de l\'annonce.');
        }

        return $this->redirectToRoute('admin.annonce.index');
    }
}
