<?php

namespace App\Controller\Admin;

use App\Entity\Event;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;

class EventCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Event::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('new', 'Ajouter un spectacle')
            ->setFormThemes(['@EasyAdmin/crud/form_theme.html.twig'])
            ->overrideTemplate('crud/new', 'admin/event/new.html.twig');
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('titre', 'Titre du spectacle')
                ->setHelp('Nom complet ou titre d’affiche du spectacle.'),

            TextareaField::new('description', 'Description')
                ->setHelp('Résumé, ambiance ou info complémentaire')
                ->hideOnIndex(),

            TextField::new('lieu', 'Lieu')
                ->setHelp('Ex : Salle théâtre – Étage de l’Omnisport'),

            ImageField::new('imageUrl', 'Affiche (fichier image)')
                ->setBasePath('/uploads/images')
                ->setUploadDir('public/uploads/images')
                ->setUploadedFileNamePattern('[slug]-[timestamp].[extension]')
                ->setHelp('Format conseillé : JPG ou PNG, max 2 Mo')
                ->setRequired(false)
                ->onlyOnForms(),

            ImageField::new('imageUrl', 'Affiche')
                ->setBasePath('/uploads/images')
                ->onlyOnIndex(),

            BooleanField::new('isVisible', 'Visible sur le site ?')
                ->setHelp('Activez pour rendre le spectacle public.'),

            CollectionField::new('dates', 'Dates de représentation')
                ->allowAdd()
                ->allowDelete()
                ->setEntryType(DateTimeType::class)
                ->setFormTypeOptions([
                    'entry_options' => [
                        'label' => false,
                        'widget' => 'single_text',
                        'html5' => true,
                    ],
                ])
                ->setHelp('Ajoutez chaque représentation avec sa date et heure.')
                ->onlyOnForms(),
        ];
    }
}
