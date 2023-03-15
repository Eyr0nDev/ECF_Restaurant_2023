<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Plats;
use App\Repository\PlatsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SingleMealsController extends AbstractController
{
    public function __construct( private EntityManagerInterface $entityManager)
    {
    }
    /*
        #[Route('/carte', name: 'app_singleMeals')]
        public function index( PlatsRepository $platsRepository): Response
        {

             $plats = $this->entityManager->getRepository(Plats::class)->findAll();

            return $this->render('single_meals/SingleMeal.html.twig',array(
                'plats'=>$plats
            ));
        }*/

    #[Route('/carte', name: 'app_singleMeals')]
    public function index( PlatsRepository $platsRepository): Response
    {
        $plats = $this->entityManager->getRepository(Plats::class)->findAll();
        $categories = $this->entityManager->getRepository(Category::class)->findAll();

        return $this->render('single_meals/SingleMeal.html.twig', array(
            'plats' => $plats,
            'categories' => $categories,
        ));
    }
}


