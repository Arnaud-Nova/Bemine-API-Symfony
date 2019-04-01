<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Repository\ReceptionTableRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
* @Route("/brides/table/", name="table_")
*/
class ReceptionTableController extends AbstractController
{
    /**
     * @Route("list", name="list", methods={"GET"})
     */
    public function list(Request $request, UserRepository $userRepository, ReceptionTableRepository $receptionTableRepository)
    {

        // récupération du wedding correspondant au user grâce à AuthenticatedListener
        $userWedding = $userRepository->findOneBy(['email' => $request->attributes->get('userEmail')])->getWedding();

        $tablesList = $receptionTableRepository->findTablesByWedding($userWedding);

        // dd($tablesList);
        $data = $tablesList;
        $response = new JsonResponse($data, 200);
        
        return $response;
    }
}
