<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Order;
use App\Entity\Carrier;
use App\Entity\Contact;
use App\Entity\Product;
use App\Entity\Category;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Constraints\Length;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

class DashboardController extends AbstractDashboardController
{
    public function configureAssets(): Assets
    {
        return Assets::new()->addCssFile('css/dashboard.css');
    }
    
    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    #[Route('/admin', name: 'admin')]
    public function index():Response
    {
        $dateNow = new \DateTime();
        $dateStart = new \DateTime();
        $dateStart->setTimestamp($dateNow->getTimestamp() - 2592000);

        $products = $this->doctrine->getRepository(Product::class)->findAll();
        $users = $this->doctrine->getRepository(User::class)->findAll();

        $allArtistArray = array();

        foreach ($users as $userInst) {
            $userId = $userInst->getId(); 
            $userRoles = $userInst->getRoles(); 

            if(in_array('ROLE_ARTIST', $userRoles))
            {
                $allArtistArray[$userId] = $userInst;
            }
        }

        $dateChart = [];
        $orderChart = [];
        $totalAll = 0;
        $ordersAll = $this->doctrine->getRepository(Order::class)->findPaidOrders();
        foreach($ordersAll as $o){  
            $dateChart[] = date_format($o->getCreatedAt(), 'j-M-y');
            $orderChart[] = 1; 
            // getChartData($dateChart, $orderChart);
            $od = $o->getOrderDetails();  
            foreach($od as $detail){
                $totalAll += $detail->getTotal();
            }                    
        }

        $totalThirty = 0;
        $ordersThirty = $this->doctrine->getRepository(Order::class)->findByThirtyDays($dateStart, $dateNow);
        foreach($ordersThirty as $o){  
            $od = $o->getOrderDetails();  
            foreach($od as $detail){
                $totalThirty += $detail->getTotal();
            }                    
        }

        // $chart = $chartBuilder->createChart(Chart::TYPE_LINE);
        // return $this->render('@EasyAdmin/page/content.html.twig');
        return $this->render('admin/dashboard.html.twig', [
            'orders' => $ordersAll,
            'total' => $totalAll,
            'orders30' => $ordersThirty,
            'total30' => $totalThirty,
            'products' => $products,
            'artists' => $allArtistArray,
            'dateC' => $dateChart,
            'orderC' => $orderChart
        ]);
    }

    // #[Route('/admin/chart', name: 'chart')]
    // public function getChartData($dateChart, $orderChart)
    // {
    //     $response = new JsonResponse(['date' => $dateChart, 'order' => $orderChart]);
    //     $response->headers->set('Content-Type', 'application/json');

    //     return $response;
    // }

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
