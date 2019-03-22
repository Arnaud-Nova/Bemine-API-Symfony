<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class WeddingController extends AbstractController
{
    //A voir si besoin de cette route, mais à priori non, que du front
    // /**
    //  * @Route("/brides/home/wedding/{id}/", name="home", requirements={"id"="\d+"}, methods={"GET"})
    //  */
    // public function home()
    // {
    //     return $this->json(
    //         [
    //             'code' => 200,
    //             'message' => 'youpi',
    //             'errors' => [],
    //             'data' => '',
    //             //'token' => 'youpi',
    //             //'userid' => 'youpi',
    //         ]
    //     );
    // }
    
    /**
     * @Route("/wedding", name="wedding")
     */
    public function index()
    {
        return $this->render('wedding/index.html.twig', [
            'controller_name' => 'WeddingController',
        ]);
    }
}
