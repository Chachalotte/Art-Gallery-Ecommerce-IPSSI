<?php

namespace App\Controller\Admin;



use App\Entity\Contact;
use Doctrine\Persistence\ManagerRegistry;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ContactCrudController extends AbstractCrudController
{
    public function __construct(ManagerRegistry $em, AdminUrlGenerator $crudUrlGenerator)
    {
        $this->em = $em;
        $this->crudUrlGenerator = $crudUrlGenerator;
    }

    public static function getEntityFqcn(): string
    {
        return Contact::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        $updateInProgress = Action::new('updateInProgress', 'En cours de traitement', 'fas fa-box-open')->linkToCrudAction('updateInProgress');
        $updateDone = Action::new('updateDone', 'Traité', 'fas fa-truck')->linkToCrudAction('updateDone');

        return $actions
            ->add('detail', $updateInProgress)
            ->add('detail', $updateDone)
            ->add('index', 'detail');
    }

    public function updateInProgress(AdminContext $context)
    {
        $contact = $context->getEntity()->getInstance();
        $contact->setStatut(2);
        $this->em->getManager()->flush();

        $this->addFlash('success', "Cette demande est en cours de traitement.");

        $url = $this->crudUrlGenerator
            ->setController(ContactCrudController::class)
            ->setAction('index')
            ->generateUrl();

        return $this->redirect($url);
    }

    public function updateDone(AdminContext $context)
    {
        $contact = $context->getEntity()->getInstance();
        $contact->setStatut(3);
        $this->em->getManager()->flush();

        $this->addFlash('success', "Cette demande est traitée");

        $url = $this->crudUrlGenerator
            ->setController(ContactCrudController::class)
            ->setAction('index')
            ->generateUrl();

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
            TextField::new('nom','Nom'),
            TextField::new('prenom','Prénom'),
            TextEditorField::new('message','Message'),
            DateTimeField::new('date','Date de la demande'),
            ChoiceField::new('statut','Statut')->setChoices([
                'Demande envoyée' => 1,
                'Demande en cours' => 2,
                'Demande traitée' => 3
            ]),
        ];
    }


    
}
