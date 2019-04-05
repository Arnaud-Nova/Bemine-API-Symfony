<?php

namespace App\Controller;

use App\Entity\ReceptionTable;
use Doctrine\ORM\EntityManager;
use App\Repository\UserRepository;
use App\Repository\PersonRepository;
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
    public function list(Request $request, UserRepository $userRepository, ReceptionTableRepository $receptionTableRepository, PersonRepository $personRepository, EntityManagerInterface $em)
    {
        // récupération du wedding correspondant au user grâce à AuthenticatedListener
        $userWedding = $userRepository->findOneBy(['email' => $request->attributes->get('userEmail')])->getWedding();

        //je crée la liste des tables
        $tablesList = $receptionTableRepository->findTablesByWedding($userWedding);

        //récupération de la table d'invités initialisée au signup
        $nameTable = 'Liste des invités';
        $tableGuestsId = $receptionTableRepository->findTableGuestsId($userWedding, $nameTable);
        $guestTable = $receptionTableRepository->find($tableGuestsId[0]['id']);

        $tablesListToSend = [];

        $i = -1;
        
        foreach ($tablesList as $table):
            $i += 1;
            $ii = -1;
            $arrayGuestIds = [];
        
            foreach ($table['people'] as $guest):
                $ii += 1;
                if($guest && $table['name'] === 'Liste des invités'){

                    $guestPerson = $personRepository->find($guest['id']);
                    $guestPerson->setSeatNumber($ii);
                    $em->persist($guestPerson);
                    $em->flush();
                    $arrayGuestIds[$guestPerson->getSeatNumber()] = $guestPerson->getId();
                } elseif ($guest) {
                   
                    $guestPersons = $personRepository->findBy(
                        ['receptionTable' => $table['id']],
                        ['seatNumber' => 'ASC']
                    );
                    $arrayGuestIds = [];
                    foreach ($guestPersons as $guestPerson):
                        if ($guestPerson->getAttendance() != 2) {
                            $arrayGuestIds[] = $guestPerson->getId();
                        } else {
                            $guestPerson->setSeatNumber(null);
                            $guestPerson->setReceptionTable($guestTable);
                            // $em->persist($guestPerson);
                            $em->flush();
                        }
                    endforeach;
                } 
            endforeach;
            
            $tablesListToSend[$i] = [
                'id' => 'table-'.$table['id'],
                'title' => $table['name'],
                'link' => $table['id'],
                'guestIds' => $arrayGuestIds
            ];
        endforeach;
        
        $data = [
            'tables' => $tablesListToSend
        ];
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
        
        $message = 'La table a bien été ajoutée.';
        $response = new JsonResponse($message, 200);
        
        return $response;
    }

    /**
     * @Route("edit", name="edit", methods={"POST"})
     */
    public function edit(Request $request, UserRepository $userRepository, ReceptionTableRepository $receptionTableRepository, PersonRepository $personRepository, EntityManagerInterface $em)
    {
        //je récupère les données du front dans l'objet request.
        $content = $request->getContent();
        $contentDecode = json_decode($content);

        // récupération du wedding correspondant au user grâce à AuthenticatedListener
        $userWedding = $userRepository->findOneBy(['email' => $request->attributes->get('userEmail')])->getWedding();

        $table = $receptionTableRepository->find($contentDecode->link);

        if (!$table){
            $message = "Il n'existe pas de table avec l'id $contentDecode->link";
                
            $response = new JsonResponse($message, 400);
       
            return $response;

        } elseif($userWedding->getId() != $receptionTableRepository->find($contentDecode->link)->getWedding()->getId()){
            $message = 'L\'id de la table n\'appartient pas au mariage du user connecté';
                
            $response = new JsonResponse($message, 400);
       
            return $response;
        }

        if ($contentDecode->title){
            $table->setName($contentDecode->title);
        }
        if ($contentDecode->guestIds){
            
            foreach ($contentDecode->guestIds as $seatNumber => $guestId):
                $person = $personRepository->find($guestId);
                $person->setSeatNumber($seatNumber);
                $person->setReceptionTable($table);
            endforeach;
        }

        $em->persist($table);
        $em->flush();
        
        $tableId = $table->getId();
        
        $message = "La table avec id : $tableId a bien été modifiée.";
        $response = new JsonResponse($message, 200);
        
        return $response;
    }

     /**
     * @Route("delete", name="delete", methods={"DELETE"})
     */
    public function delete(Request $request, UserRepository $userRepository, ReceptionTableRepository $receptionTableRepository, EntityManagerInterface $em)
    {
        //je récupère les données du front dans l'objet request.
        $content = $request->getContent();
        $contentDecode = json_decode($content);

        // récupération du wedding correspondant au user grâce à AuthenticatedListener
        $userWedding = $userRepository->findOneBy(['email' => $request->attributes->get('userEmail')])->getWedding();

        $table = $receptionTableRepository->find($contentDecode->id);

        if (!$table){
            $message = "Il n'existe pas de table avec l'id $contentDecode->id";
                
            $response = new JsonResponse($message, 400);
       
            return $response;

        } elseif($userWedding->getId() != $receptionTableRepository->find($contentDecode->id)->getWedding()->getId()){
            $message = 'L\'id de la table n\'appartient pas au mariage du user connecté';
                
            $response = new JsonResponse($message, 400);
       
            return $response;
        }
       

        $em->remove($table);
        $em->flush();
        
        $message = "La table a bien été supprimée.";
        $response = new JsonResponse($message, 200);
        
        return $response;
    }
}
