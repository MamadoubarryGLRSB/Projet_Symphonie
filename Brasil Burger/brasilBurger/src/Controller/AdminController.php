<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Entity\Commande;
use App\Repository\UserRepository;

use App\Repository\ProduitRepository;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class AdminController extends AbstractController

{
    private $repository;
    private $comerepo;
     public function __construct(ProduitRepository $repository,CommandeRepository $comerepo)
    {
        $this->repository = $repository;
        $this->comerepo = $comerepo;
    } 
    #[Route('/admin', name: 'app_admin')]
    public function index(ProduitRepository $produitRepository,PaginatorInterface $paginator,Request $request): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        } 
        $produits=$paginator->paginate($this->repository->findProduitDisponibleQuery(),
        
        $request->query->getInt('page',1),limit:4
    );
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
            'produits' => $produits,
        
        ]);
    }
    #[Route('/archive', name: 'app_archive')]
    public function archive(ProduitRepository $produitRepository,PaginatorInterface $paginator,Request $request): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        } 
        $produits=$paginator->paginate($this->repository->findProduitDisponibleQuery(),
        
        $request->query->getInt('page',1),limit:4
    );
        return $this->render('admin/archive.html.twig', [
            'controller_name' => 'AdminController',
            'produits' => $produits,
        
        ]);
    }
    #[Route('/userlist', name: 'app_userlist')]
    public function userlist(UserRepository $userRepository): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        } 
        $users=$userRepository->findAll();
        return $this->render('admin/user.html.twig', [
            'controller_name' => 'AdminController',
            'users' => $users,
        
        ]);
    }

    #[Route('/commande', name: 'app_commande')]
    public function commande(CommandeRepository $commandeRepository,UserRepository $userRepository,ProduitRepository $produitRepository,PaginatorInterface $paginator,Request $request): Response
    {
        $produits=$produitRepository->findAll();
        $commandeValide=0;
        $commandeInValide=0;
        $commandeEnCours=0;
        $produitPlusCommander=0;
        $produit=$produits[0];
        $revenus=0;
        $commandes=$paginator->paginate($this->comerepo->findCommandeDisponibleQuery(),
        
        $request->query->getInt('page',1),limit:4
    );
        $users=$userRepository->findAll();
        foreach ($commandes as  $commande) {
            # code...
            if($commande->getEtat()=='Terminé')
            {
                $commandeValide++;
                $revenus+=$commande->getPrix();


            }elseif($commande->getEtat()=='Annulé')
            {
                $commandeInValide++;

            }else
            {
                $commandeEnCours++;

            }


        }
        foreach ($produits as $pr) {
            if(count($produit->getCommandes()) < count($pr->getCommandes()))
            {
                $produit=$pr;
                

            }
            # code...
        }
        return $this->render('admin/commande.html.twig', [
            'controller_name' => 'AdminController',
            'commandes' => $commandes,
            'users' => $users,
            'produits' => $produits,
            'commandeValide' => $commandeValide,
            'commandeInValide' =>$commandeInValide,
            'commandeEnCours' => $commandeEnCours,
            'produitPlusCommander'=>$produit,
            'revenus'=>$revenus
            
        ]);
    }

    #[Route('/commande/vl/{id}', name: 'valid_command')]
    //#[IsGranted('ROLE_ADMIN', message: 'No access! Vous navez pas les autorisations nécéssaires pour accéder à cette page!! Get out!',statusCode: 403)]
    public function valideCommande(Commande $commande = null,
                                        EntityManagerInterface $em,
                                      CommandeRepository $repo
                                      ): Response
    {

        if($commande){
            $commande->setEtat('Terminé');
    
        $this->addFlash('success','COMMANDE VALIDÉE AVEC SUCCES!! UN MAIL A ETE ENVOYÉ AU DESTINATAIRE');

            $em->persist($commande);//
            $em->flush();
            return $this->redirectToRoute('app_commande');
        }
        return $this->render('admin/commande.html.twig', [
            'controller_name' => 'AdminController',
            'commande' => $commande,
        ]);
    }
    #[Route('/commande/rm/{id}', name: 'remov_command')]
    //#[IsGranted('ROLE_ADMIN', message: 'No access! Vous navez pas les autorisations nécéssaires pour accéder à cette page!! Get out!',statusCode: 403)]
    public function removCommande(Commande $commande = null,
                                        EntityManagerInterface $em,
                                      CommandeRepository $repo
                                      ): Response
    {

        if($commande){
            $commande->setEtat('Annulé');
    
        $this->addFlash('danger','COMMANDE ANNULÉE AVEC SUCCES!!');

            $em->persist($commande);//
            $em->flush();
            return $this->redirectToRoute('app_commande');
        }
        return $this->render('admin/commande.html.twig', [
            'controller_name' => 'AdminController',
            'commande' => $commande,
        ]);
    }

    #[Route('/produit/ar/{id}', name: 'archive_produit')]
    //#[IsGranted('ROLE_ADMIN', message: 'No access! Vous navez pas les autorisations nécéssaires pour accéder à cette page!! Get out!',statusCode: 403)]
    public function archiveProduit(Produit $produit = null,
                                        EntityManagerInterface $em,
                                      ProduitRepository $repo
                                      ): Response
    {

        if($produit){
            $produit->setEtat('indisponible');
    
        $this->addFlash('success','Produit Archivé');

            $em->persist($produit);//
            $em->flush();
            return $this->redirectToRoute('app_admin');
        }
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
            'produit' => $produit,
        ]);
    }
    #[Route('/produit/dr/{id}', name: 'desarchive_produit')]
    //#[IsGranted('ROLE_ADMIN', message: 'No access! Vous navez pas les autorisations nécéssaires pour accéder à cette page!! Get out!',statusCode: 403)]
    public function desarchiverProduit(Produit $produit = null,
                                        EntityManagerInterface $em,
                                      ProduitRepository $repo
                                      ): Response
    {

        if($produit){
            $produit->setEtat('disponible');
    
        $this->addFlash('sucess','Produit Desarchivé avec SUCCES!!');

            $em->persist($produit);//
            $em->flush();
            return $this->redirectToRoute('app_admin');
        }
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
            'produit' => $produit,
        ]);
    }
    
}
