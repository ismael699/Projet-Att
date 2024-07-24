<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app.accueil')]
    public function accueil(): Response
    {
        return $this->render('Home/accueil.html.twig');
    }

    #[Route('/', name: 'app.home')]
    public function index(): Response
    {
        return $this->render('Home/index.html.twig');
    }
}