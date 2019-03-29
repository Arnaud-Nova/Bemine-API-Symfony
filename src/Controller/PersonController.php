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
     * @Route("/brides/guests/list/wedding/{id}", name="indexGuests", requirements={"id"="\d+"}, methods={"GET"})
     */
    public function indexGuests(PersonRepository $personRepository, $id)
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

    // /**
    //  * @Route("/brides/guests/new/wedding/{id}", name="new", requirements={"id"="\d+"}, methods={"GET", "POST"})
    //  */
    // public function new(Request $request, PersonRepository $personRepository, WeddingRepository $weddingRepository, $id, EventRepository $eventRepository, GuestGroupRepository $guestGroupRepository)
    // {
    //     //je récupère les données du front dans l'objet request.
    //     $content = $request->getContent();
    //     $contentDecode = json_decode($content);

    //     $wedding = $weddingRepository->find($id);
        
    //     if (!$wedding){
    //         $data = 
    //         [
    //             'message' => 'Le wedding id n\'existe pas.'
    //         ]
    //         ;
            
    //         $response = new JsonResponse($data, 400);
        
    //         return $response;
    //     }

    //     $person = new Person();
    //     $person->setLastname($contentDecode->lastname);
    //     $person->setFirstname($contentDecode->firstname);
    //     $person->setWedding($wedding);
    //     $person->setNewlyweds(0);
    //     $person->setAttendance(0);

    //     $guestGroup = new GuestGroup();
    //     $guestGroup->setWedding($wedding);

    //     //j'assigne les events au groupe
    //     $events = $eventRepository->findEventsByWedding($id);

    //     foreach ($contentDecode->events as $eventId=>$eventValue){
    //         if ($eventValue === true){
    //             $thisEvent = $eventRepository->find($eventId);
    //             $guestGroup->addEvent($thisEvent);
    //         }
    //     }

    //     $entityManager = $this->getDoctrine()->getManager();
    //     $entityManager->persist($person);
    //     $guestGroup->setContactPerson($person);

    //     $alreayUser = $guestGroupRepository->findByEmail($contentDecode->email);
    //     if ($alreayUser){
    //         $data = 
    //         [
    //             'message' => 'l\'email du user existe déjà.'
    //         ]
    //         ;

    //         $response = new JsonResponse($data, 400);
        
    //         return $response;
            
    //     }

    //     if ($contentDecode->email){
    //         $guestGroup->setEmail($contentDecode->email);
    //     };

    //     $entityManager->persist($guestGroup);

    //     $person->setGuestGroup($guestGroup);
    //     $entityManager->persist($person);
        
    //     foreach ($contentDecode->people as $person){
    //         $addPerson = new Person();
    //         $addPerson->setLastname($person->lastname);
    //         $addPerson->setFirstname($person->firstname);
    //         $addPerson->setWedding($wedding);
    //         $addPerson->setNewlyweds(0);
    //         $addPerson->setGuestGroup($guestGroup);
    //         $addPerson->setAttendance(0);

    //         $entityManager->persist($addPerson);
    //     } 

    //     $entityManager->flush();

    //     // $guestGroupId = $guestGroup->getId();
    //     // $guestGroupCreated = $guestGroupRepository->findByGuestGroupIdQueryBuilder($guestGroupId);

    //     $eventsType = $eventRepository->findEventsByWedding($id);

    //     $data = 
    //         [
    //             // 'guestGroupCreated' => $guestGroupCreated,
    //             'events' => $eventsType
    //         ]
    //     ;

    //     $response = new JsonResponse($data, 200);       
    //     return $response;

    // }

    // /**
    //  * @Route("/brides/guests/edit/{id}", name="editGuestGroup", requirements={"id"="\d+"}, methods={"GET", "POST"})
    //  */
    // public function editGuestGroup(GuestGroupRepository $guestGroupRepository, PersonRepository $personRepository, $id, Request $request)
    // {
    //     // $guestGroup = $personRepository->findByGuestGroup($id);
    //     $guestGroup = $guestGroupRepository->find($id);
        
    //     if (!$guestGroup){
    //         $data = 
    //         [
    //             'message' => 'Le guestGroupId n\'existe pas'
    //         ]
    //         ;

    //         $response = new JsonResponse($data, 400);
        
    //         return $response;
    //     }

    //     //je récupère les données du front dans l'objet request.
    //     $content = $request->getContent();
    //     $contentDecode = json_decode($content);

    //     $entityManager = $this->getDoctrine()->getManager();

    //     //edit email 
    //     if ($guestGroup->getId() === $contentDecode->group->id){
    //         $guestGroup->setEmail($contentDecode->group->email);
    //         $entityManager->persist($guestGroup);
    //     }   
        

    //     //edit persons
    //     foreach ($contentDecode->group->people as $person){
    //         $personBdd = $personRepository->find($person->id);
    //         $personBdd->setFirstname($person->firstname);
    //         $personBdd->setLastname($person->lastname);
    //         $personBdd->setAttendance($person->attendance);
    //         $entityManager->persist($personBdd);
    //     }
        
    //     // dd($guestGroup);

    //     $entityManager->flush();
    //     $guestGroupArray = $guestGroupRepository->findByGuestGroupIdQueryBuilder($id);
        
    //     $data = 
    //         [
    //             'guestGroupEdited' => $guestGroupArray
    //         ]
    //     ;

    //     $response = new JsonResponse($data, 200);       
    //     return $response;
    // }

    // /**
    //  * @Route("/brides/guests/delete/{id}", name="deleteGuestGroup", requirements={"id"="\d+"}, methods={"DELETE"})
    //  */
    // public function deleteGuestGroup(GuestGroupRepository $guestGroupRepository, PersonRepository $personRepository, $id, Request $request)
    // {
    //     // $guestGroup = $personRepository->findByGuestGroup($id);
    //     $guestGroup = $guestGroupRepository->find($id);

    //     if (!$guestGroup){
    //         $data = 
    //         [
    //             'message' => 'Le guestGroupId n\'existe pas'
    //         ]
    //         ;

    //         $response = new JsonResponse($data, 400);
        
    //         return $response;
    //     }

    //     dd($guestGroup->getPeople());
    //     //je récupère les données du front dans l'objet request.
    //     $content = $request->getContent();
    //     $contentDecode = json_decode($content);

    //     $entityManager = $this->getDoctrine()->getManager();

    //     //edit email 
    //     if ($guestGroup->getId() === $contentDecode->group->id){
    //         $guestGroup->setEmail($contentDecode->group->email);
    //         $entityManager->persist($guestGroup);
    //     }   
        

    //     //edit persons
    //     foreach ($contentDecode->group->people as $person){
    //         $personBdd = $personRepository->find($person->id);
    //         $personBdd->setFirstname($person->firstname);
    //         $personBdd->setLastname($person->lastname);
    //         $personBdd->setAttendance($person->attendance);
    //         $entityManager->persist($personBdd);
    //     }
        
        
        
    //     // dd($guestGroup);

    //     $entityManager->flush();
    //     $guestGroupArray = $guestGroupRepository->findByGuestGroupQueryBuilder($id);
        
    //     $data = 
    //         [
    //             'group' => $guestGroupArray
    //         ]
    //     ;

    //     $response = new JsonResponse($data, 200);       
    //     return $response;
    // }
}
