<?php

namespace App\Controller;

use App\Entity\ReceptionTable;
use Doctrine\ORM\EntityManager;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
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

    /**
     * @Route("show", name="show", methods={"POST"})
     */
    public function show(Request $request, UserRepository $userRepository, ReceptionTableRepository $receptionTableRepository)
    {
        //je récupère les données du front dans l'objet request.
        $content = $request->getContent();
        $contentDecode = json_decode($content);

        // récupération du wedding correspondant au user grâce à AuthenticatedListener
        $userWedding = $userRepository->findOneBy(['email' => $request->attributes->get('userEmail')])->getWedding();

        $oneTable = $receptionTableRepository->findOneTableById($contentDecode->id);
        
        if (!$oneTable){
            $message = 'Il n\'y a pas de table avec l\'id correspondant.';
                
            $response = new JsonResponse($message, 400);
       
            return $response;

        } elseif($userWedding->getId() != $receptionTableRepository->find($contentDecode->id)->getWedding()->getId()){
            $message = 'L\'id de la table n\'appartient pas au mariage du user connecté';
                
            $response = new JsonResponse($message, 400);
       
            return $response;
        }
        
        // dd($oneTable);
        $data = $oneTable;
        $response = new JsonResponse($data, 200);
        
        return $response;
    }

    /**
     * @Route("new", name="new", methods={"POST"})
     */
    public function new(Request $request, UserRepository $userRepository, ReceptionTableRepository $receptionTableRepository, EntityManagerInterface $em)
    {
        //je récupère les données du front dans l'objet request.
        $content = $request->getContent();
        $contentDecode = json_decode($content);

        // récupération du wedding correspondant au user grâce à AuthenticatedListener
        $userWedding = $userRepository->findOneBy(['email' => $request->attributes->get('userEmail')])->getWedding();

        $table = new ReceptionTable();
        $table->setWedding($userWedding);
        $table->setName($contentDecode->name);
        $table->setTotalSeats($contentDecode->totalSeats);

        $em->persist($table);
        $em->flush();
        

        // dd($oneTable);
        $message = 'La table a bien été ajoutée.';
        $response = new JsonResponse($message, 200);
        
        return $response;
    }

    /**
     * @Route("edit", name="edit", methods={"POST"})
     */
    public function edit(Request $request, UserRepository $userRepository, ReceptionTableRepository $receptionTableRepository, EntityManagerInterface $em)
    {
        //je récupère les données du front dans l'objet request.
        $content = $request->getContent();
        $contentDecode = json_decode($content);

        // récupération du wedding correspondant au user grâce à AuthenticatedListener
        $userWedding = $userRepository->findOneBy(['email' => $request->attributes->get('userEmail')])->getWedding();

        $table = $receptionTableRepository->find($contentDecode->id);

        if (!$table){
            $message = 'Il n\'y a pas de table avec l\'id correspondant.';
                
            $response = new JsonResponse($message, 400);
       
            return $response;

        } elseif($userWedding->getId() != $receptionTableRepository->find($contentDecode->id)->getWedding()->getId()){
            $message = 'L\'id de la table n\'appartient pas au mariage du user connecté';
                
            $response = new JsonResponse($message, 400);
       
            return $response;
        }

        if ($contentDecode->name){
            $table->setName($contentDecode->name);
        }
        if ($contentDecode->totalSeats){
            $table->setTotalSeats($contentDecode->totalSeats);
        }

        $em->persist($table);
        $em->flush();
        
        $tableId = $table->getId();
        // dd($oneTable);
        $message = "La table avec id : $tableId a bien été modifiée.";
        $response = new JsonResponse($message, 200);
        
        return $response;
    }
}
