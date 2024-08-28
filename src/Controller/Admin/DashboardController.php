<?php

namespace App\Controller\Admin;

use App\Repository\AnnonceRepository;
use App\Repository\CityRepository;
use App\Repository\UserRepository;
use App\Repository\ServiceRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/dashboard', name: 'admin.dashboard', methods: ['GET'])]
class DashboardController extends AbstractController
{
    #[Route('/', name: '.index')]
    public function index(UserRepository $userRepo, CityRepository $cityRepo, ServiceRepository $serviceRepo, AnnonceRepository $annonceRepo): Response
    {
        $nbrUser = $userRepo->countUsers();
        $nbrCity = $cityRepo->countCitys();
        $nbrService = $serviceRepo->countServices();
        $nbrAnnonce = $annonceRepo->countAnnonces();

        return $this->render('Admin/Dashboard/index.html.twig', [
            'nbrUser' => $nbrUser,
            'nbrCity' => $nbrCity,
            'nbrService' => $nbrService,
            'nbrAnnonce' => $nbrAnnonce,
        ]);
    }
}
