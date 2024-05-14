<?php

namespace App\Controller;

use App\Entity\Categories;
use App\Form\CategorieType;
use App\Repository\CategoriesRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CategoriesController extends AbstractController
{
    #[Route('/admin/categories', name: 'admin_categories')]
    public function index(CategoriesRepository $rep): Response
    {
        $categorie=$rep->findAll();
        return $this->render('categories/index.html.twig', [
            'categorie' => $categorie,
        ]);
    }

    #[Route('/admin/categories/create', name: 'admin_categories_create')]
    public function create(EntityManagerInterface $en ,Request $request): Response
    {
        //affichage de l'objet formulaire
        $categorie=new Categories();
        $form=$this->createForm(CategorieType::class,$categorie);
        //traitement des données
        $form->handleRequest($request);
        if($form->isSubmitted() and $form->isValid())
        {
            $en->persist($categorie);
            $en->flush();
            return $this->redirectToRoute('admin_categories');

        }
        return $this->render('categories/create.html.twig', [
            'f' => $form,
        ]);
    }

    #[Route('/admin/categories/update/{id}', name: 'admin_categories_update')]
    public function update(Categories $categorie, EntityManagerInterface $en ,Request $request): Response
    {
        //affichage de l'objet formulaire
        $form=$this->createForm(CategorieType::class,$categorie);
        //traitement des données
        $form->handleRequest($request);
        if($form->isSubmitted() and $form->isValid())
        {
            $en->persist($categorie);
            $en->flush();
            return $this->redirectToRoute('admin_categories');

        }
        return $this->render('categories/update.html.twig', [
            'f' => $form,
        ]);
    }


    #[Route('/admin/categories/delete/{id}', name: 'admin_categories_delete')]
public function delete(Categories $cat, EntityManagerInterface $entityManager): Response
{
    
    if($cat->getLivres()){
        $livres = $cat->getLivres();
        foreach ($livres as $livre) {
            $cat->removeLivre($livre);
        }
    }

    $entityManager->remove($cat);

    $entityManager->flush();

    return $this->redirectToRoute('admin_categories');
}

}
