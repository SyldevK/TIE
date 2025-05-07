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

class EventCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Event::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),

            TextField::new('titre', 'Titre'),

            TextareaField::new('description', 'Description')
                ->hideOnIndex(),

            TextField::new('lieu', 'Lieu'),

            ImageField::new('imageUrl', 'Affiche')
                ->setBasePath('/uploads/images/')
                ->setUploadDir('public/uploads/images/')
                ->setUploadedFileNamePattern('[slug]-[timestamp].[extension]')
                ->setRequired(false),


            BooleanField::new('isVisible', 'Visible ?'),

            CollectionField::new('dates', 'Dates de représentation')
                ->allowAdd()
                ->allowDelete()
                ->setEntryType(DateTimeType::class)
                ->setFormTypeOptions([
                    'entry_options' => [
                        'label' => false,
                        'widget' => 'single_text',
                    ],
                ])
                ->onlyOnForms(),
        ];
    }
}
