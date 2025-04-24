<?php

namespace App\Controller\Admin;

use App\Entity\Media;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;


class MediaCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Media::class;
    }

    public function configureFields(string $pageName): iterable
    {
        $fields = [];

        // Champ image
        $fields[] = ImageField::new('url', 'Aperçu')
            ->setBasePath('/uploads/images')
            ->onlyOnIndex(); // affiché uniquement dans la liste

        $fields[] = ImageField::new('url', 'Fichier')
            ->setBasePath('/uploads/images')
            ->setUploadDir('public/uploads/images')
            ->setUploadedFileNamePattern('[slug]-[timestamp].[extension]')
            ->onlyOnForms(); // formulaire uniquement

        // Champ type
        $fields[] = TextField::new('type', 'Type de média');

        return $fields;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(TextFilter::new('type', 'Type de média'));
    }
}
