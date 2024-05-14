<?php

namespace App\Controller;

use App\Entity\Livres;
use App\Form\LivreType;
use App\Repository\LivresRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LivresController extends AbstractController
{
    #[Route('/admin/livres', name: 'admin_livres')]
    public function index(LivresRepository $rep): Response
    {
        $livres=$rep->findAll();
       //$livres=$rep->findGreaterThan(60);
       // dd($livres);
        return $this->render('livres/index.html.twig', [
            'livres'=>$livres,
        ]);
    }

    #[Route('/admin/livres/show/{id}', name: 'admin_livres_show')]
    public function show(Livres $livre): Response
    {
        return $this->render('livres/show.html.twig', [
            'livre'=>$livre,
        ]);
    }

    #[Route('/admin/livres/create', name: 'admin_livres_create')]
    public function create(EntityManagerInterface $em): Response
    {
        $livre=new Livres();
        $livre->setImage('https://fastly.picsum.photos/id/695/300/300.jpg?hmac=6Sh_ZQoCQx4j-pNIfzKgLhZhNegGY3XK2PkI5MMeGHY')
              ->setTitre('Titre du livre 9')
              ->setEditeur('Editeur 1')
              ->setISBN('111.222.335.445')
              ->setPrix(150)
              ->setEditedAt(new \DateTimeImmutable('01-01-2024'))
              ->setSlug('titre-du-livre-5')
              ->setResumer('kjhgfdsdfghjkllkjhgfdsdfghjklkjhgfdfghjk');
        $em->persist($livre);
        $em->flush();
       // return $this->render('livres/create.html.twig', [
       //     'livre'=>$livre,
       // ]);
        return $this->redirectToRoute('admin_livres');
       // dd($livre);
    }

    #[Route('/admin/livres/delete/{id}', name: 'admin_livres_delete')]
    public function delete(EntityManagerInterface $em, Livres $livre): Response
    {
        //recherche de livre a supprimer
       // $livre=$rep->find($id);
        //suppression du livre trouvé
        $em->remove($livre) ;
        $em->flush();
       // dd($livre);
       // return $this->redirectToRoute('livres/create.html.twig', [
       //     'livre'=>$livre,
       // ]);
       return $this->redirectToRoute('admin_livres');
    }
    #[Route('/admin/livres/add', name: 'admin_livre_add')]
    public function add(EntityManagerInterface $en ,Request $request): Response
    {
        //affichage de l'objet formulaire
        $livre=new Livres();
        $form=$this->createForm(LivreType::class,$livre);
        //traitement des données
        $form->handleRequest($request);
        if($form->isSubmitted() and $form->isValid())
        {
            $en->persist($livre);
            $en->flush();
            return $this->redirectToRoute('admin_livres');

        }
        return $this->render('livres/add.html.twig', [
            'f' => $form,
        ]);
    }
}
