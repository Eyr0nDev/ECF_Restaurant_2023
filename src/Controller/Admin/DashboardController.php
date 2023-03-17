<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\OpeningHours;
use App\Entity\Plats;
use App\Entity\Restaurant;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    public function __construct(
        private AdminUrlGenerator $adminUrlGenerator
    ){
    }


    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {

        $url = $this->adminUrlGenerator
            ->setController(PlatsCrudController::class)
            ->generateUrl();
        return $this->redirect($url);

        /* Option 2. You can make your dashboard redirect to different pages depending on the user

         if ('jane' === $this->getUser()->getUsername()) {
             return $this->redirect('...');
         }

         Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
         (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)

        return $this->render('some/path/my-dashboard.html.twig'); */
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('LeQuaiAntique');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        // section Menu / carte
        yield MenuItem::section('Menus');
        yield MenuItem::subMenu('Actions', 'fas fa-bars')->setSubItems([
            MenuItem::linkToCrud('Ajouter un plat', 'fas fa-plus',Plats::class)->setAction(Crud::PAGE_NEW),
            MenuItem::linkToCrud('Voir les plats', 'fas fa-eye',Plats::class)
        ]);
        //section Categories
        yield MenuItem::section('Réservations');
        yield MenuItem::subMenu('Actions', 'fas fa-bars')->setSubItems([
            MenuItem::linkToCrud('Gérer les horaires d\'ouverture', 'fas fa-eye',OpeningHours::class),
            MenuItem::linkToCrud('Gérer les restaurants', 'fas fa-eye',Restaurant::class)
            ]);

        // Section Reservations
        yield MenuItem::section('Catégories');
        yield MenuItem::subMenu('Actions', 'fas fa-bars')->setSubItems([
            MenuItem::linkToCrud('Ajouter une catégorie', 'fas fa-plus',Category::class)->setAction(Crud::PAGE_NEW),
            MenuItem::linkToCrud('Voir les catégories', 'fas fa-eye',Category::class)
        ]);
        // section Utilisateurs
        yield MenuItem::section('Utilisateurs');
        yield MenuItem::subMenu('Actions', 'fas fa-bars')->setSubItems([
            MenuItem::linkToCrud('Ajouter un utilisateur', 'fas fa-plus',User::class)->setAction(Crud::PAGE_NEW),
            MenuItem::linkToCrud('Voir les utilisateurs', 'fas fa-eye',User::class)
        ]);


        // yield MenuItem::linkToCrud('The Label', 'fas fa-list', EntityClass::class);
    }
}
