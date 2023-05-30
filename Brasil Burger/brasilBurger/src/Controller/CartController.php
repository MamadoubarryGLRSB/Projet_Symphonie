<?php

namespace App\Controller;

use DateTime;
use DateTimeZone;
use App\Entity\Produit;
use App\Entity\Commande;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CartController extends AbstractController
{
   /**
     * @Route("/cart", name="app_cart")
     */
    public function index(): Response
    {
        return $this->render('cart/index.html.twig', [
            'controller_name' => 'CartController',
        ]);
    }

    /**
     * @Route("/add/{id}", name="cart_add")
     */
    public function add(Produit $produit, SessionInterface $session)
    {
        // On récupère le panier actuel
        $panier = $session->get("panier", []);
        $id = $produit->getId();

        if(!empty($panier[$id])){
            $panier[$id]++;
        }else{
            $panier[$id] = 1;
        }

        // On sauvegarde dans la session
        $session->set("panier", $panier);

        return $this->redirectToRoute("produit_panier");
    }

    /**
     * @Route("/remove/{id}", name="remove")
     */
    public function remove(Produit $produit, SessionInterface $session)
    {
        // On récupère le panier actuel
        $panier = $session->get("panier", []);
        $id = $produit->getId();

        if(!empty($panier[$id])){
            if($panier[$id] > 1){
                $panier[$id]--;
            }else{
                unset($panier[$id]);
            }
        }

        // On sauvegarde dans la session
        $session->set("panier", $panier);

        return $this->redirectToRoute("produit_panier");
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(Produit $produit, SessionInterface $session)
    {
        // On récupère le panier actuel
        $panier = $session->get("panier", []);
        $id = $produit->getId();

        if(!empty($panier[$id])){
            unset($panier[$id]);
        }

        // On sauvegarde dans la session
        $session->set("panier", $panier);

        return $this->redirectToRoute("produit_panier");
    }

    /**
     * @Route("/delete", name="delete_all")
     */
    public function deleteAll(SessionInterface $session)
    {
        $session->remove("panier");

        return $this->redirectToRoute("produit_panier");
    }


     /**
     * @Route("/panier", name="produit_panier")
     */
    public function panier(SessionInterface $session, ProduitRepository $produitRepo)
    {
    
        $panier = $session->get("panier", []);


        //on fabrique les données
        $dataPanier = [];
        $total = 0;
        foreach ($panier as $id => $quantite) {
            # code...
            $produit = $produitRepo->find($id);
            $dataPanier[] = [
                "produit" => $produit,
                "quantite" => $quantite
            ];
            $total += $produit->getPrix() * $quantite;
        }
        //trier par utlisateur
        $user = $this->getUser();

        return $this->render("cart/index.html.twig", compact("dataPanier", "total", 'user'));
    }

      /**
     * @Route("/save", name="produit_save_all")
     */
    //depuis bd
    public function saveAll(ProduitRepository $produitRepo, SessionInterface $session, EntityManagerInterface $manager)
    {


        $panier = $session->get("panier", []);

        //on fabrique les données
        $dataPanier = [];
        $total = 0;
        $commande = new Commande();
        foreach ($panier as $id => $quantite) {
            $produit = $produitRepo->find($id);
            $commande->addProduit($produit);
            $dataPanier[] = [
                "produit" => $produit,
                "quantite" => $quantite
            ];
            $total += $produit->getPrix() * $quantite;
            //dd($commande);
        }
        $date = new DateTime('now');
        $date->setTimezone(new DateTimeZone('Africa/Dakar'));
        $commande
            ->setDate($date)
            ->setEtat("En cours")
            //->setHeure($date)
            ->setPrix('' . $total)
            ->setUsers($this->getUser());
        $manager->persist($commande);
        $manager->flush();

        //$session->set('commande',$commande);

        return $this->redirectToRoute("app_acceuil");
    }
}
