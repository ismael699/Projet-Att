<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/dashboard', name: 'admin.dashboard', methods: ['GET'])]
class DashboardController extends AbstractController
{
    #[Route('/', name: '.index')]
    public function index(): Response
    {
        return $this->render('Admin/Dashboard/index.html.twig');
    }
}
