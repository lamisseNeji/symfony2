<?php

namespace App\Controller;

use App\Repository\LivresRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AllLivresController extends AbstractController
{
    #[Route('/all/livres', name: 'app_all_livres')]
    public function lister(LivresRepository $rep): Response
    {
        $livres=$rep->findAll();
        return $this->render('all_livres/index.html.twig', [
            'livres' => $livres,
        ]);
    }

    #[Route('/all/livres/show/{id}', name: 'app_livre_details')]
    public function details(LivresRepository $rep,$id)
    {
       $livre=$rep->find($id);
       return $this->render('all_livres/detailsLivre.html.twig',[
        'livre'=>$livre,
       ]);
    }
}
