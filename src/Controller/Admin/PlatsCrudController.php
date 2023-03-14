<?php

namespace App\Controller\Admin;

use App\Entity\Plats;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class PlatsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Plats::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('name', 'Nom'),
            MoneyField::new('price', 'Prix')->setCurrency('EUR')
                ->setStoredAsCents( false),
        ];
    }
}