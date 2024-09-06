<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app.home')]
    public function index(): Response
    {
        return $this->render('Home/index.html.twig');
    }

    #[Route('/propos', name: 'app.propos')]
    public function aPropos(): Response
    {
        return $this->render('Home/propos.html.twig');
    }

    #[Route('/aide', name: 'app.aide')]
    public function aide(): Response
    {
        return $this->render('Home/aide.html.twig');
    }

    #[Route('/cgv', name: 'app.cgv')]
    public function cgv(): Response
    {
        return $this->render('Home/cgv.html.twig');
    }

    #[Route('/politique', name: 'app.politique')]
    public function politique(): Response
    {
        return $this->render('Home/politique.html.twig');
    }
}