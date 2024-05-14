<?php

namespace App\Controller;

use App\Entity\Panier;
use App\Entity\User;
use App\Entity\LignePanier;
//use App\Entity\LigneCommande;
use App\Repository\LivresRepository;
use App\Repository\PanierRepository;
//use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
//use App\Repository\LigneCommandeRepository;
use ContainerD4ZwGPx\getLivresService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PanierController extends AbstractController
{
    #[Route('/panier', name: 'app_panier')]
    public function index(): Response
    {
        return $this->render('panier/index.html.twig', [
            'controller_name' => 'PanierController',
        ]);
    }
    
    #[Route('/panier/{id}', name: 'panier_ajouter')]
    public function ajouterPanier(int $id, LivresRepository $livRep, PanierRepository $panierRep,EntityManagerInterface $manager): Response
    {
        $livre=$livRep->find($id);
        $user = $this->getUser();
        $panier = $panierRep->findOneBy(['user'=>$user]);

        if (!$panier) {
            $panier = new Panier();
            $panier->setUser($user);
        }
        $lignePanier = null;
        foreach ($panier->getLignePaniers() as $ligne) {
            if ($ligne->getLivre()->getId() === $livre->getId()) {
                $lignePanier = $ligne;
                break;
            }
        }

        if ($lignePanier) {
            $lignePanier->setQuantite($lignePanier->getQuantite() + 1);
        } else{
        $lignePanier = new LignePanier();
        $lignePanier->setLivre($livre);
        $lignePanier->setQuantite(1);

        
        $manager->persist($lignePanier);
        $panier->addLignePanier($lignePanier);
    }
        $manager->persist($panier);
        $manager->flush();

        return $this->redirectToRoute('panier_afficher');
    }

    #[Route('/profile/panier/{id}/retirer', name: 'panier_retirer')]
    public function retirerUnDuPanier(int $id, LivresRepository $livrep, PanierRepository $panierRep, EntityManagerInterface $manager): Response
    {
        $livre = $livrep->find($id);
        $user = $this->getUser();
        $panier = $panierRep->findOneBy(['user' => $user]);

        if ($panier) {
            $lignePanier = null;
            foreach ($panier->getLignePaniers() as $ligne) {
                if ($ligne->getLivre()->getId() === $livre->getId()) {
                    $lignePanier = $ligne;
                    break;
                }
            }

            if ($lignePanier && $lignePanier->getQuantite() >= 1) {
                $lignePanier->setQuantite($lignePanier->getQuantite() - 1);
            } elseif ($lignePanier && $lignePanier->getQuantite() == 1) {
                $panier->removeLignePanier($lignePanier);
                $manager->remove($lignePanier);
            } else {
                $this->addFlash('error', 'La quantité ne peut pas être négative.');
            }

            $manager->flush();
        }
        return $this->redirectToRoute('panier_afficher');
    }
    
    #[Route('/profile/panier', name: 'panier_afficher')]
public function afficherPanier(PanierRepository $panierRepository): Response
{
    $user = $this->getUser(); 
    $panier = $panierRepository->findOneBy(['user' => $user]);

    $total = 0;
    foreach ($panier->getLignePaniers() as $lignePanier) {
        $total += $lignePanier->getLivre()->getPrix() * $lignePanier->getQuantite();
    }

    return $this->render('panier/index.html.twig', [
        'panier' => $panier,
        'total' => $total,
    ]);
}

#[Route('/panier/{id}/delete', name: 'panier_supprimer')]
public function delete(int $id, LivresRepository $livRep, PanierRepository $panierRep, EntityManagerInterface $manager): Response
{
    
    $livre = $livRep->find($id);
   
    if (!$livre) {
        throw $this->createNotFoundException('Le livre avec l\'identifiant '.$id.' n\'existe pas.');
    }

    $user = $this->getUser();
    $panier = $panierRep->findOneBy(['user' => $user]);

   
    $lignePanier = null;
    foreach ($panier->getLignePaniers() as $ligne) {
        if ($ligne->getLivre()->getId() === $livre->getId()) {
            $lignePanier = $ligne;
            break;
        }
    }

    if ($lignePanier) {
      
        $panier->removeLignePanier($lignePanier);
        
       
        $manager->persist($panier);
        $manager->flush();
    } else {
        // Si la ligne du panier n'est pas trouvée, générer une exception
        throw $this->createNotFoundException('Le livre avec l\'identifiant '.$id.' n\'a pas été trouvé dans le panier.');
    }

    // Rediriger vers la page du panier
    return $this->redirectToRoute('panier_afficher');
}
#[Route('/panier/tout/delete', name: 'supprimer_tout_panier')]
    public function deletePanier( LivresRepository $livRep, PanierRepository $panierRep, EntityManagerInterface $manager): Response
    {
        $user = $this->getUser();
        $panier = $panierRep->findOneBy(['user' => $user]);

        foreach ($panier->getLignePaniers() as $lignePanier) {
            $panier->removeLignePanier($lignePanier);
            $manager->remove($lignePanier);
        }

        $manager->persist($panier);
        $manager->flush();

        return $this->redirectToRoute('panier_afficher');
    }
}
