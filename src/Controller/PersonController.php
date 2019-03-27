<?php

namespace App\Controller;

use App\Entity\Person;
use App\Entity\GuestGroup;
use App\Repository\EventRepository;
use App\Repository\PersonRepository;
use App\Repository\WeddingRepository;
use App\Repository\GuestGroupRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
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
    public function new(Request $request, PersonRepository $personRepository, WeddingRepository $weddingRepository, $id, EventRepository $eventRepository)
    {
        //je récupère les données du front dans l'objet request.
        $content = $request->getContent();
        $contentDecode = json_decode($content);

        $wedding = $weddingRepository->find($id);
        
        if (!$wedding){
            $message = 'Le wedding id n\'existe pas';
            $response = new Response($message, 404);
            $response->headers->set('Content-Type', 'application/json');
           
            return $response;
        }

        $person = new Person();
        $person->setLastname($contentDecode->lastname);
        $person->setFirstname($contentDecode->firstname);
        $person->setWedding($wedding);
        $person->setNewlyweds(0);
        $person->setAttendance(0);

        $guestGroup = new GuestGroup();
        $guestGroup->setWedding($wedding);

        //j'assigne les events au groupe

        // for ($i = 1; $i <= 4; $i++){
        //     foreach ($contentDecode->events as $eventValue){
        //         $participate = $eventValue->$i;
        //             $event = $eventRepository->find($i);
        //             // dd($participate, $event);
        //             if ($participate == true){
        //                 // dd($participate, $event);
        //                 $guestGroup->addEvent($event);
        //             }
        //     } 
        // } 

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($person);
        $guestGroup->setContactPerson($person);

        if ($contentDecode->email){
            $guestGroup->setEmail($contentDecode->email);
        };

        $entityManager->persist($guestGroup);

        $person->setGuestGroup($guestGroup);
        $entityManager->persist($person);
        
        foreach ($contentDecode->people as $person){
            $addPerson = new Person();
            $addPerson->setLastname($person->lastname);
            $addPerson->setFirstname($person->firstname);
            $addPerson->setWedding($wedding);
            $addPerson->setNewlyweds(0);
            $addPerson->setGuestGroup($guestGroup);
            $addPerson->setAttendance(0);

            $entityManager->persist($addPerson);
        } 

        $entityManager->flush();

        $response = new JsonResponse('', 200);       
        return $response;

    }

    /**
     * @Route("/brides/guests/edit/{id}", name="edit", requirements={"id"="\d+"}, methods={"GET", "POST"})
     */
    public function edit(GuestGroupRepository $guestGroupRepository, PersonRepository $personRepository, $id)
    {
        $guestGroup = $personRepository->findByGuestGroup($id);

        dd($guestGroup);
        
        if (!$guestGroup){
            $message = 'Le guestGroupId n\'existe pas';
            $response = new Response($message, 404);
            $response->headers->set('Content-Type', 'application/json');
           
            return $response;
        }

        //je récupère les données du front dans l'objet request.
        $content = $request->getContent();
        $contentDecode = json_decode($content);

        $response = new JsonResponse('', 200);       
        return $response;
    }
}
