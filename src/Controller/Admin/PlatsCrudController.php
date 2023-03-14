<?php

namespace App\Controller\Admin;

use App\Entity\Plats;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
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
            AssociationField::new('Category', 'Catégorie'),
            DateTimeField::new('updatedAt')->hideOnForm(),
            DateTimeField::new('createdAt')->hideOnForm()
        ];
    }
    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Plats) return;

        $entityInstance->setUpdatedAt(new \DateTimeImmutable);
        parent::updateEntity($entityManager, $entityInstance);
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Plats) return;

        $entityInstance->setCreatedAt(new \DateTimeImmutable);

        parent::persistEntity($entityManager, $entityInstance);
    }
}
