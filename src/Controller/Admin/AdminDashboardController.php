<?php

namespace App\Controller\Admin;

use App\Entity\Media;
use App\Entity\User;
use App\Entity\Event;
use App\Entity\Reservation;
use App\Controller\Admin\EventCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class AdminDashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);

        // Redirige vers la page de gestion des événements (tu peux changer EventCrudController par un autre)
        $url = $adminUrlGenerator->setController(EventCrudController::class)->generateUrl();

        return $this->redirect($url);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('🎭 Théâtre TIE - Admin')
            ->renderContentMaximized()
            ->disableDarkMode();
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linktoDashboard('Tableau de bord', 'fa fa-home');

        yield MenuItem::section('Gestion');
        yield MenuItem::linkToCrud('Utilisateurs', 'fa fa-users', User::class);
        yield MenuItem::linkToCrud('Événements', 'fa fa-calendar', Event::class);
        yield MenuItem::linkToCrud('Réservations', 'fa fa-ticket', Reservation::class);
        yield MenuItem::linkToCrud('Médias', 'fa fa-image', Media::class);

        yield MenuItem::section('Retour au site');
        yield MenuItem::linkToUrl('Voir le site public', 'fa fa-arrow-left', '/');
    }
}
