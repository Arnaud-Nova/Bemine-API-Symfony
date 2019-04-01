<?php

namespace App\Controller;

use App\Entity\Person;
use App\Entity\GuestGroup;
use App\Repository\UserRepository;
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
     * @Route("/brides/group/new", name="newGroup", methods={"POST"})
     */
    public function newGroup(Request $request, PersonRepository $personRepository, WeddingRepository $weddingRepository, EventRepository $eventRepository, GuestGroupRepository $guestGroupRepository, UserRepository $userRepository)
    {
        //je récupère les données du front dans l'objet request.
        $content = $request->getContent();
        $contentDecode = json_decode($content);

        // récupération du wedding correspondant au user grâce à AuthenticatedListener
        $userWedding = $userRepository->findOneBy(['email' => $request->attributes->get('userEmail')])->getWedding();

        $wedding = $weddingRepository->find($userWedding);
        
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
        $events = $eventRepository->findEventsByWedding($userWedding);

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

        // $eventsType = $eventRepository->findEventsByWedding($id);

        $message = 'Le group a bien été ajouté';

        $response = new JsonResponse($message, 200);       
        return $response;

    }

    /**
     * @Route("/brides/guests/group", name="show_group", methods={"POST"})
     */
    public function showGroup(GuestGroupRepository $guestGroupRepository, Request $request, UserRepository $userRepository)
    {

        //je récupère les données du front dans l'objet request.
        $content = $request->getContent();
        $contentDecode = json_decode($content);

        // récupération du wedding correspondant au user grâce à AuthenticatedListener
        $userWedding = $userRepository->findOneBy(['email' => $request->attributes->get('userEmail')])->getWedding();

        $guestGroupArray = $guestGroupRepository->findByGuestGroupIdQueryBuilder($contentDecode->id); //groupId modifié en id
        
        $guestGroup = $guestGroupRepository->find($contentDecode->id);
        
        if (!$guestGroupArray){
            $message = 'Le guestGroupId n\'existe pas';

            $response = new JsonResponse($message, 400);
        
            return $response;
        } elseif ($userWedding->getId() != $guestGroup->getWedding()->getId()) {
            $message = 'Le guestGroupId donné n\'est pas relié au wedding du user connecté';

            $response = new JsonResponse($message, 400);
        
            return $response;
        }

        
        $data = $guestGroupArray[0];

        $response = new JsonResponse($data, 200);       
        return $response;
    }

    /**
     * @Route("/brides/group/edit", name="edit_group", requirements={"id"="\d+"}, methods={"POST"})
     */
    public function editGroup(GuestGroupRepository $guestGroupRepository, Request $request, UserRepository $userRepository)
    {
        //je récupère les données du front dans l'objet request.
        $content = $request->getContent();
        $contentDecode = json_decode($content);

        // récupération du wedding correspondant au user grâce à AuthenticatedListener
        $userWedding = $userRepository->findOneBy(['email' => $request->attributes->get('userEmail')])->getWedding();

        //je récupère le guestGroup 
        $guestGroup = $guestGroupRepository->find($contentDecode->id);   //groupId modifié en id
        
        if (!$guestGroup){
            $message = 'Le guestGroupId n\'existe pas';

            $response = new JsonResponse($message, 400);
            return $response;

        }elseif ($userWedding->getId() != $guestGroup->getWedding()->getId()) {
            $message = 'Le guestGroupId donné n\'est pas relié au wedding du user connecté';

            $response = new JsonResponse($message, 400);
        
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
        // $guestGroup = $guestGroupRepository->find($contentDecode->id);   //groupId modifié en id
        $guestGroup = $guestGroupRepository->findOneBy(['id' => $contentDecode->id]);
        
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

     /**
     * @Route("/brides/group/mail/wedding/{id}", name="index_mail", requirements={"id"="\d+"}, methods={"GET","POST"})
     */
    public function indexMail(GuestGroupRepository $guestGroupRepository, PersonRepository $personRepository, $id, WeddingRepository $weddingRepository)
    {
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
        
        // $contactsGroup = $guestGroupRepository->findGroupsQueryBuilder($id);
        $groups = $guestGroupRepository->findGroupAndContactPerson($id);

        //mariés exclus de ces comptes
        $countTotalGuests = $personRepository->findTotalGuestsCountQueryBuilder($id);
        $countPresent = $personRepository->findAttendancePresentCountQueryBuilder($id);
        $countAbsent = $personRepository->findAttendanceAbsentCountQueryBuilder($id);
        //problème sur la requête, les autres fonctionnent bien avec le param id du wedding, mais celle-ci renvoie un count incorrect...
        $countWaiting = $personRepository->findAttendanceWaitingCountQueryBuilder($id);

        // $mails = $mailRepository->findAllQueryBuilder();
        
        $data = 
            [
                // 'mails' => $mails,
                'groups' => $groups,
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
     * @Route("/guests/show/{slugUrl}", name="show_website", requirements={"id"="\d+"}, methods={"GET"})
     */
    public function showWebsite(GuestGroupRepository $guestGroupRepository, PersonRepository $personRepository, EventRepository $eventRepository, $slugUrl)
    {
        $guestGroupForWebsite = $guestGroupRepository->findGuestGroupForWebsite($slugUrl);
        $thisWedding = $guestGroupRepository->findThisWeddingBySlug($slugUrl);
        $newlyweds = $personRepository->findByNewlywedsForWebsite($thisWedding);
        $eventsForThisGroup = $eventRepository->findEventsActiveByWedding($thisWedding);
        
        //si besoin de tout avoir au même niveau sauf les people
        // $arrayResult = array_merge($guestGroupForWebsite, $newlyweds, $eventsForThisGroup);

        

        if (!$guestGroupForWebsite){
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
                'thisGroup' => $guestGroupForWebsite,
                'newlyweds' => $newlyweds,
                'eventsForThisGroup' => $eventsForThisGroup
            ]
        ;

        $response = new JsonResponse($data, 200);       
        return $response;
    }
}
