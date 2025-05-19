<?php

namespace App\Controller\Admin;

use App\Entity\PlanningCours;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;


class PlanningCoursCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return PlanningCours::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('groupe', 'Groupe (ex: Enfants, Ados…)'),

            ChoiceField::new('jour', 'Jour')
                ->setChoices([
                    'Lundi' => 'Lundi',
                    'Mardi' => 'Mardi',
                    'Mercredi' => 'Mercredi',
                    'Jeudi' => 'Jeudi',
                    'Vendredi' => 'Vendredi',
                    'Samedi' => 'Samedi',
                    'Dimanche' => 'Dimanche',
                ]),

            TimeField::new('heureDebut', 'Heure de début'),
            TimeField::new('heureFin', 'Heure de fin'),

            TextField::new('lieu', 'Lieu')->setRequired(false),

            ChoiceField::new('type', 'Type de session')
                ->setChoices([
                    'Cours' => 'cours',
                    'Répétition' => 'répétition',
                ])
        ];
    }
}
