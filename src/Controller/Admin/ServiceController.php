<?php

namespace App\Controller\Admin;

use App\Entity\Service;
use App\Form\ServiceType;
use App\Repository\ServiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/service', name: 'admin.service', methods: ['GET'])]
class ServiceController extends AbstractController
{
    #[Route('/', name: '.index')]
    public function index(ServiceRepository $serviceRepo): Response
    {
        return $this->render('Admin/Service/index.html.twig', [
            'services' => $serviceRepo->findAllOrderedByCreatedAt(),
        ]);
    }

    #[Route('/create', name: '.create', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $em): Response 
    {
        $service = new Service();

        $form = $this->createForm(ServiceType::class, $service);
        $form->handleRequest($request); 

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($service);
            $em->flush();

            $this->addFlash('success', 'Service créer avec succès.');

            return $this->redirectToRoute('admin.service.index');
        }

        return $this->render('Admin/Service/create.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/{id}/edit', name: '.edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Service $service, EntityManagerInterface $em): Response 
    {
        $form = $this->createForm(ServiceType::class, $service);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Service modifié avec succès.');
            return $this->redirectToRoute('admin.service.index');
        }

        return $this->render('Admin/Service/edit.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: '.delete', methods: ['POST'])]
    public function delete(Service $service, Request $request, EntityManagerInterface $em): Response 
    {
        if ($this->isCsrfTokenValid('delete' . $service->getId(), $request->request->get('_token'))) {
            $em->remove($service);
            $em->flush();

            $this->addFlash('success', 'Service supprimé avec succès.');
        } else {
            $this->addFlash('error', 'Erreur lors de la suppression du service.');
        }

        return $this->redirectToRoute('admin.service.index');
    }
}
