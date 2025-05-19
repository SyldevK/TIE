<?php

namespace App\Controller\Admin;

use App\Entity\Enrollment;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;

class EnrollmentCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Enrollment::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable(Action::NEW, Action::EDIT, Action::DELETE); // lecture seule
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            // Affiche le prénom + nom du participant
            TextField::new('nomCompletParticipant', 'Participant'),

            TextField::new('groupe', 'Groupe'),
            TextField::new('anneeScolaire', 'Année scolaire'),
            BooleanField::new('isActive', 'Actif'),

            TextField::new('nomCompletUser', 'Utilisateur associé'),
        ];
    }
}
