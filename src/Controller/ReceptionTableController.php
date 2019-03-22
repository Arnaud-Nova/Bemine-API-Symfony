<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ReceptionTableController extends AbstractController
{
    /**
     * @Route("/reception/table", name="reception_table")
     */
    public function index()
    {
        return $this->render('reception_table/index.html.twig', [
            'controller_name' => 'ReceptionTableController',
        ]);
    }
}
