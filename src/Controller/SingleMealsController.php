<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SingleMealsController extends AbstractController
{
    #[Route('/single/meals', name: 'app_single_meals')]
    public function index(): Response
    {
        return $this->render('single_meals/index.html.twig', [
            'controller_name' => 'SingleMealsController',
        ]);
    }
}
