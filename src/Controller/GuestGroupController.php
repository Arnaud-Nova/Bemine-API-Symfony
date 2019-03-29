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
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class GuestGroupController extends AbstractController
{

    /**
     * @Route("/brides/group/new/wedding/{id}", name="newGroup", requirements={"id"="\d+"}, methods={"GET", "POST"})
     */
    public function newGroup(Request $request, PersonRepository $personRepository, WeddingRepository $weddingRepository, $id, EventRepository $eventRepository, GuestGroupRepository $guestGroupRepository)
    {
        //je récupère les données du front dans l'objet request.
        $content = $request->getContent();
        $contentDecode = json_decode($content);

        $wedding = $weddingRepository->find($id);
        
        if (!$wedding){
            $data = 
            [
                'message' => 'Le wedding id n\'existe pas.'
            ]
            ;
            
            $response = new JsonResponse($data, 400);
        
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
        $events = $eventRepository->findEventsByWedding($id);

        foreach ($contentDecode->events as $eventId=>$eventValue){
            if ($eventValue === true){
                $thisEvent = $eventRepository->find($eventId);
                $guestGroup->addEvent($thisEvent);
            }
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($person);
        $guestGroup->setContactPerson($person);

        $alreayUser = $guestGroupRepository->findByEmail($contentDecode->email);
        if ($alreayUser){
            $data = 
            [
                'message' => 'l\'email du user existe déjà.'
            ]
            ;

            $response = new JsonResponse($data, 400);
        
            return $response;
            
        }

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

        // $guestGroupId = $guestGroup->getId();
        // $guestGroupCreated = $guestGroupRepository->findByGuestGroupIdQueryBuilder($guestGroupId);

        $eventsType = $eventRepository->findEventsByWedding($id);

        $data = 
            [
                // 'guestGroupCreated' => $guestGroupCreated,
                'events' => $eventsType
            ]
        ;

        $response = new JsonResponse($data, 200);       
        return $response;

    }

    /**
     * @Route("/brides/guests/group/{groupId}", name="show_group", requirements={"id"="\d+"}, methods={"GET"})
     */
    public function showGroup(GuestGroupRepository $guestGroupRepository, $groupId)
    {
        $guestGroupArray = $guestGroupRepository->findByGuestGroupIdQueryBuilder($groupId);
        
        if (!$guestGroupArray){
            $data = 
            [
                'message' => 'Le guestGroupId n\'existe pas'
            ]
            ;

            $response = new JsonResponse($data, 400);
        
            return $response;
        }

        
        $data = 
            [
                'guestGroup' => $guestGroupArray
            ]
        ;

        $response = new JsonResponse($data, 200);       
        return $response;
    }

    /**
     * @Route("/brides/group/edit", name="edit_group", requirements={"id"="\d+"}, methods={"GET", "POST"})
     */
    public function editGroup(GuestGroupRepository $guestGroupRepository, Request $request)
    {
        //je récupère les données du front dans l'objet request.
        $content = $request->getContent();
        $contentDecode = json_decode($content);

        //je récupère le guestGroup 
        $guestGroup = $guestGroupRepository->find($contentDecode->groupId);   
        
        if (!$guestGroup){
            $data = 
            [
                'message' => 'Le guestGroupId n\'existe pas'
            ]
            ;

            $response = new JsonResponse($data, 400);
        
            return $response;
        }

        
        $guestGroup->setEmail($contentDecode->email);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($guestGroup);
        $entityManager->flush();

        
        $data = 
            [
                'message' => 'La modification a bien été prise en compte.'
            ]
        ;

        $response = new JsonResponse($data, 200);
       
        return $response;

    }

    /**
     * @Route("/brides/group/delete", name="delete_group", requirements={"id"="\d+"}, methods={"DELETE"})
     */
    public function deleteGroup(GuestGroupRepository $guestGroupRepository, Request $request)
    {
        //je récupère les données du front dans l'objet request.
        $content = $request->getContent();
        $contentDecode = json_decode($content);
        
        //je récupère le guestGroup 
        $guestGroup = $guestGroupRepository->find($contentDecode->groupId);   
        
        if (!$guestGroup){
            $data = 
            [
                'message' => 'Le guestGroupId n\'existe pas'
            ]
            ;

            $response = new JsonResponse($data, 400);
        
            return $response;
        }


        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($guestGroup);
        $entityManager->flush();

        
        $data = 
            [
                'message' => 'La suppression a bien été prise en compte.'
            ]
        ;

        $response = new JsonResponse($data, 200);
       
        return $response;

    }

    
}
