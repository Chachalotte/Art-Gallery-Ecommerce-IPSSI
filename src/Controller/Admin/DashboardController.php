<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Order;
use App\Entity\Carrier;
use App\Entity\Product;
use App\Entity\Category;
use App\Entity\Contact;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index():Response
    {
        // $chart = $chartBuilder->createChart(Chart::TYPE_LINE);
        // return $this->render('@EasyAdmin/page/content.html.twig');
        return $this->render('admin/dashboard.html.twig', []);
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
            MenuItem::linkToCrud('Orders', 'fa fa-shopping-cart', Order::class),
            MenuItem::linkToCrud('Contact', 'fa fa-solid fa-comments', Contact::class),

            MenuItem::section('Liste liées aux produits'),
            MenuItem::linkToCrud('Produits', 'fa fa-tags', Product::class),
            MenuItem::linkToCrud('Categories', 'fa fa-list', Category::class),
            MenuItem::linkToCrud('Carriers', 'fa fa-truck', Carrier::class),

            MenuItem::linkToLogout('Logout', 'fa fa-exit'),
        ];
    }
}
