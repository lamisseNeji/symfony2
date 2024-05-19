<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


class PaymentController extends AbstractController
{
    #[Route('/payment', name: 'app_payment')]
    public function index(): Response
    {
        return $this->render('payment/index.html.twig', [
            'controller_name' => 'PaymentController',
        ]);
    }

    
    #[Route('/checkout/{total}', name: 'checkout')]
    public function checkout($stripeSK,$total): Response
    {
        $stripe = new \Stripe\StripeClient($stripeSK); 
        $session = $stripe->checkout->sessions->create([
          'line_items' => [[
            'price_data' => [
              'currency' => 'usd',
              'product_data' => [
                'name' => 'Total  ',
              ],
                'unit_amount' =>$total ,
              ],
              'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $this->generateUrl('success_url',[],UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' =>  $this->generateUrl('cancel_url',[],UrlGeneratorInterface::ABSOLUTE_URL),
          ]);
          return $this->redirect($session->url,303);
          
        
    }

    #[Route('/success-url', name: 'success_url')]
    public function successUrl(): Response
    {
        return $this->render('payment/succes.html.twig', [
            
        ]);
    }
    
    #[Route('/cancel-url', name: 'cancel_url')]
    public function cancelsUrl(): Response
    {
        return $this->render('payment/cancel.html.twig', [
            
        ]);
    }
}
