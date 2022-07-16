<?php

namespace App\Controller\Admin;

use App\Entity\Carrier;
use App\Entity\Category;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Entity\Product;


class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        return $this->render('@EasyAdmin/page/content.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('The art factory');
    }

    public function configureMenuItems(): iterable
    {
        return [

            //=========================================================================================
            //Sections à gauche du menu admin
            //=========================================================================================            
            MenuItem::section('Liste liées aux utilisateurs'),
            MenuItem::linkToCrud('Utilisateurs', 'fa fa-user', User::class),

            MenuItem::section('Liste liées aux produits'),
            MenuItem::linkToCrud('Produits', 'fa fa-tags', Product::class),
            MenuItem::linkToCrud('Categories', 'fa fa-list', Category::class),
            MenuItem::linkToCrud('Carriers', 'fa fa-truck', Carrier::class),

            MenuItem::linkToLogout('Logout', 'fa fa-exit'),
        ];
    }
}
