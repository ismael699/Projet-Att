<?php

namespace App\Controller\Admin;

use App\Entity\City;
use App\Form\CityType;
use App\Repository\CityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/city', name: 'admin.city', methods: ['GET'])]
class CityController extends AbstractController
{
    #[Route('/', name: '.index')]
    public function index(CityRepository $cityRepo): Response
    {
        return $this->render('Admin/City/index.html.twig', [
            'cityes' => $cityRepo->findAll(),
        ]);
    }

    #[Route('/create', name: '.create', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $em): Response 
    {
        $city = new City();

        $form = $this->createForm(CityType::class, $city);
        $form->handleRequest($request); //  traite la requête et met à jour l'entité avec les données soumises du formulaire

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($city);
            $em->flush();

            $this->addFlash('success', 'Ville créer avec succès.');

            return $this->redirectToRoute('admin.city.index');
        }

        return $this->render('Admin/City/create.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/{id}/edit', name: '.edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, City $city, EntityManagerInterface $em): Response 
    {
        $form = $this->createForm(CityType::class, $city);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Ville modifié avec succès.');
            return $this->redirectToRoute('admin.city.index');
        }

        return $this->render('Admin/City/edit.html.twig', [
            'user' => $city,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: '.delete', methods: ['POST'])]
    public function delete(City $city, Request $request, EntityManagerInterface $em): Response 
    {
        if ($this->isCsrfTokenValid('delete' . $city->getId(), $request->request->get('_token'))) {
            $em->remove($city);
            $em->flush();

            $this->addFlash('success', 'Ville supprimé avec succès.');
        } else {
            $this->addFlash('error', 'Erreur lors de la suppression de la ville.');
        }

        return $this->redirectToRoute('admin.city.index');
    }
}
