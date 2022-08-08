<?php

namespace App\Controller\Admin;

use App\Data\Mail;
use App\Entity\Order;
use Doctrine\Persistence\ManagerRegistry;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;

class OrderCrudController extends AbstractCrudController
{
    private $em;
    private $crudUrlGenerator;

    public function __construct(ManagerRegistry $em, AdminUrlGenerator $crudUrlGenerator)
    {
        $this->em = $em;
        $this->crudUrlGenerator = $crudUrlGenerator;
    }

    public static function getEntityFqcn(): string
    {
        return Order::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        $updatePreparation = Action::new('updatePreparation', 'Préparation en cours', 'fas fa-box-open')->linkToCrudAction('updatePreparation');
        $updateDelivery = Action::new('updateDelivery', 'Livraison en cours', 'fas fa-truck')->linkToCrudAction('updateDelivery');

        return $actions
            ->add('detail', $updatePreparation)
            ->add('detail', $updateDelivery)
            ->add('index', 'detail');
    }

    public function updatePreparation(AdminContext $context)
    {
        $order = $context->getEntity()->getInstance();
        $order->setState(2);
        $this->em->getManager()->flush();

        $this->addFlash('success', "La commande ".$order->getReference()." est bien en cours de péparation");

        $url = $this->crudUrlGenerator
            ->setController(OrderCrudController::class)
            ->setAction('index')
            ->generateUrl();

        //envoi d'email mailjet
        $mail = new Mail();
        $content = $order->getUser()->getFirstname().' '.$order->getUser()->getName().", <br/>Votre commande n°".$order->getReference()." chez The art Factory est en cours de péparation";
        $mail->send($order->getUser()->getEmail(), $order->getUser()->getFirstname().' '.$order->getUser()->getName(), 'Votre commande The art Factory est bien validée', $content);

        return $this->redirect($url);
    }

    public function updateDelivery(AdminContext $context)
    {
        $order = $context->getEntity()->getInstance();
        $order->setState(3);
        $this->em->getManager()->flush();

        $this->addFlash('success', "La commande ".$order->getReference()." est bien en cours de livraison");

        $url = $this->crudUrlGenerator
            ->setController(OrderCrudController::class)
            ->setAction('index')
            ->generateUrl();

        //envoi d'email mailjet
        $mail = new Mail();
        $content = $order->getUser()->getFirstname().' '.$order->getUser()->getName().", <br/>Votre commande n°".$order->getReference()." chez The art Factory est en cours de péparation";
        $mail->send($order->getUser()->getEmail(), $order->getUser()->getFirstname().' '.$order->getUser()->getName(), 'Votre commande The art Factory est bien validée', $content);

        return $this->redirect($url);
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setDefaultSort(['id' => 'DESC']);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            DateTimeField::new('createdAt', 'Passé le'),
            TextField::new('user.firstname', 'Prénom de l\'utilisateur'),
            TextField::new('user.name', 'Nom de l\'utilisateur'),
            TextEditorField::new('delivery', 'Adresse de livraison')->onlyOnDetail(),
            MoneyField::new('total', 'Total produit')->setCurrency('EUR'),
            MoneyField::new('carrierPrice', 'Frais de port')->setCurrency('EUR'),
            ChoiceField::new('state')->setChoices([
                'Non payée' => 0,
                'Payée' => 1,
                'Préparation en cours' => 2,
                'Livraison en cours' => 3
            ]),
            ArrayField::new('orderDetails', 'Produits achetés')
        ];
    }
}
