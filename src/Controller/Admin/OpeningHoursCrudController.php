<?php

namespace App\Controller\Admin;

use App\Entity\OpeningHours;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TimeField;

class OpeningHoursCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return OpeningHours::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('day_of_week', 'Jour de la semaine'),
            TimeField::new('lunch_open_time','Heure d\'ouverture du midi'),
            TimeField::new('lunch_close_time', 'Heure de fermeture du midi'),
            TimeField::new('dinner_open_time','Heure d\'ouverture du soir'),
            TimeField::new('dinner_close_time', 'Heure de fermeture du soir'),
            BooleanField::new('is_closed')
        ];
    }
}
