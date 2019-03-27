<?php

namespace App\Controller;

use App\Entity\Person;
use App\Entity\GuestGroup;
use App\Repository\PersonRepository;
use App\Repository\WeddingRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;


class PersonController extends AbstractController
{
    /**
     * @Route("/brides/guests/list/wedding/{id}", name="index", requirements={"id"="\d+"}, methods={"GET"})
     */
    public function index(PersonRepository $personRepository, $id)
    {
        
        $guests = $personRepository->findAllQueryBuilder($id);
         //mariés exclus de ces comptes
        $countTotalGuests = $personRepository->findTotalGuestsCountQueryBuilder($id);
        $countPresent = $personRepository->findAttendancePresentCountQueryBuilder($id);
        $countAbsent = $personRepository->findAttendanceAbsentCountQueryBuilder($id);
        //problème sur la requête, les autres fonctionnent bien avec le param id du wedding, mais celle-ci renvoie un count incorrect...
        $countWaiting = $personRepository->findAttendanceWaitingCountQueryBuilder($id);
        
        if (!$guests){
            $message = 'Le wedding id n\'existe pas';
            $response = new Response($message, 404);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
        
        $data = 
            [
                'guests' => $guests,
                'countTotalGuests' => $countTotalGuests,
                'countPresent' => $countPresent,
                'countAbsent' => $countAbsent,
                'countWaiting' => $countWaiting
            ]
        ;
        $response = new JsonResponse($data, 200);
       
        return $response;
    }

    /**
     * @Route("/brides/guests/new/wedding/{id}", name="new", requirements={"id"="\d+"}, methods={"GET", "POST"})
     */
    public function new(Request $request, PersonRepository $personRepository, WeddingRepository $weddingRepository, $id)
    {
        //je récupère les données du front dans l'objet request.
        $content = $request->getContent();
        $contentDecode = json_decode($content);

        $wedding = $weddingRepository->find($id);
        

        $person = new Person();
        $person->setLastname($contentDecode->lastname);
        $person->setFirstname($contentDecode->firstname);
        $person->setWedding($wedding);
        $person->setNewlyweds(0);

        $guestGroup = new GuestGroup();
        $guestGroup->setWedding($wedding);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($person);
        $guestGroup->setContactPerson($person);

        if ($contentDecode->email){
            $guestGroup->setEmail($contentDecode->email);
        };

        $entityManager->persist($guestGroup);

        $person->setGuestGroup($guestGroup);
        $entityManager->persist($person);
        
        $entityManager->flush();
       
        
        return $this->json(
            [
                'code' => 200,
                'message' => 'youpi',
                'errors' => [],
                'data' => [
                    
                ],
                //'token' => 'youpi',
                //'userid' => 'youpi',
            ]
        );
    }
}
