<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Repository\ProduitRepository;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AcceuilController extends AbstractController
{
    private $repository;
    public function __construct(ProduitRepository $repository)
   {
       $this->repository = $repository;
   } 


    #[Route('/acceuil', name: 'app_acceuil')]
    public function index(ProduitRepository $produitRepository,PaginatorInterface $paginator,Request $request): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        } 

         $produits=$paginator->paginate($this->repository->findProduitDisponibleQuery(),
        
        $request->query->getInt('page',1),limit:4,
    );
        return $this->render('acceuil/index.html.twig', [
            'controller_name' => 'AcceuilController',
            'produits' => $produits,
            
        ]);
    }
    #[Route('/liste_reserv_user', name: 'app_liste_reservation')]
    public function liste_commande(ProduitRepository $produitRepository,CommandeRepository $repo): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        } 
        $user = $this->getUser();
        $produits=$produitRepository ->findAll();
        $commandes=$repo->findAll();
        //Afficher la liste des commandes par utilisateurs
        $commandes=$repo->findby(['users'=>$user]);
        return $this->render('acceuil/liste.html.twig', [
            'controller_name' => 'AcceuilController',
            'commandes' => $commandes,
            'produits'=>$produits,
        ]);
    }
    #[Route('/commande_user/rm/{id}', name: 'remov_command_user')]
    //#[IsGranted('ROLE_ADMIN', message: 'No access! Vous navez pas les autorisations nécéssaires pour accéder à cette page!! Get out!',statusCode: 403)]
    public function removCommandeUser(Commande $commande = null,
                                        EntityManagerInterface $em,
                                      CommandeRepository $repo
                                      ): Response
    {

        if($commande){
            $commande->setEtat('Annulé');
    
        $this->addFlash('danger','COMMANDE ANNULÉE AVEC SUCCES!!');

            $em->persist($commande);//
            $em->flush();
            return $this->redirectToRoute('app_liste_reservation');
        }
        return $this->render('acceuil/liste.html.twig', [
            'controller_name' => 'AcceuilController',
            'commande' => $commande,
        ]);
    }
}
