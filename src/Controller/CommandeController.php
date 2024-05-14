<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Repository\CommandeRepository;
use App\Repository\PanierRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\LigneCommandeRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\LigneCommande;

class CommandeController extends AbstractController
{


    #[Route('/profile/commande/valider', name: 'valider_commande')]
public function ValiderCommande(PanierRepository $panierRep,EntityManagerInterface $manager): Response
{
    $user = $this->getUser(); 
    $panier= $panierRep->findOneBy(['user' => $user]);
    $commande= new Commande();
    $commande->setUser($user);

    foreach ($panier->getLignePaniers() as $lignePanier) {
        $ligneCommande = new LigneCommande();
        $ligneCommande->setCommande($commande);
        $ligneCommande->setLivre($lignePanier->getLivre());
        $ligneCommande->setQuantite($lignePanier->getQuantite());

        
        $livre=$ligneCommande->getLivre();
        $livre->setQte($livre->getQte() - $ligneCommande->getQuantite());
        
        $manager->persist($ligneCommande);
        $commande->addLigneCommande($ligneCommande);
        
        
    }

    $manager->persist($commande);
    $manager->flush();

    foreach ($panier->getLignePaniers() as $lignePanier) {
        $manager->remove($lignePanier);
    }
    $manager->flush();


    return $this->redirectToRoute('afficher_commande', ['id' => $commande->getId()]);
}


#[Route('/profile/commande/afficher/{id}', name: 'afficher_commande')]
public function afficherCommande(int $id, CommandeRepository $commandeRepository): Response
{
    $commande = $commandeRepository->find($id);

    return $this->render('commande/index.html.twig', [
        'commande' => $commande,
    ]);
}

}


