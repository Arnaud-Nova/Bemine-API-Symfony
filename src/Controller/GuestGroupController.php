<?php

namespace App\Controller;

use App\Entity\Person;
use App\Entity\GuestGroup;
use App\Utils\RandomString;
use App\Entity\ReceptionTable;
use App\Repository\UserRepository;
use App\Repository\EventRepository;
use App\Repository\PersonRepository;
use App\Repository\WeddingRepository;
use App\Repository\GuestGroupRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ReceptionTableRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class GuestGroupController extends AbstractController
{

    /**
     * @Route("/brides/group/new", name="newGroup", methods={"POST"})
     */
    public function newGroup(Request $request, ReceptionTableRepository $receptionTableRepository, PersonRepository $personRepository, WeddingRepository $weddingRepository, EventRepository $eventRepository, GuestGroupRepository $guestGroupRepository, UserRepository $userRepository, RandomString $rS)
    {
        //je récupère les données du front dans l'objet request.
        $content = $request->getContent();
        $contentDecode = json_decode($content);

        // récupération du wedding correspondant au user grâce à AuthenticatedListener
        $userWedding = $userRepository->findOneBy(['email' => $request->attributes->get('userEmail')])->getWedding();

        //récupération de la table d'invités initialisée au signup
        $nameTable = 'Liste des invités';
        $tableGuestsId = $receptionTableRepository->findTableGuestsId($userWedding, $nameTable);
        // dd($tableGuestsId);
        $table = $receptionTableRepository->find($tableGuestsId[0]['id']);
        

        $person = new Person();
        $person->setLastname($contentDecode->lastname);
        $person->setFirstname($contentDecode->firstname);
        $person->setWedding($userWedding);
        $person->setNewlyweds(0);
        $person->setAttendance(0);
        $person->setReceptionTable($table);

        $guestGroup = new GuestGroup();
        $guestGroup->setWedding($userWedding);
        $guestGroup->setSlugUrl($rS->random());

        $message = [];

        foreach ($contentDecode->events as $eventId=>$eventValue){
            if ($eventValue === true){
                $thisEvent = $eventRepository->find($eventId);
                if ($thisEvent->getWedding() == $userWedding) {
                    $guestGroup->addEvent($thisEvent);
                } else {
                    $message[] = 'L\'event avec l\'id ' . $eventId . 'ne correspond pas à ce mariage';
                }
            }
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($person);
        $guestGroup->setContactPerson($person);

        $alreadyUsedEmail = $guestGroupRepository->findByEmail($contentDecode->email);
        if ($alreadyUsedEmail){
            $message[] = 'l\'email du group existe déjà.';
        }

        if ($contentDecode->email) {
            $guestGroup->setEmail($contentDecode->email);
        };

        $entityManager->persist($guestGroup);

        $person->setGuestGroup($guestGroup);
        $entityManager->persist($person);
        
        foreach ($contentDecode->people as $person){
            $addPerson = new Person();
            $addPerson->setLastname($person->lastname);
            $addPerson->setFirstname($person->firstname);
            $addPerson->setWedding($userWedding);
            $addPerson->setNewlyweds(0);
            $addPerson->setGuestGroup($guestGroup);
            $addPerson->setAttendance(0);
            $addPerson->setReceptionTable($table);

            $entityManager->persist($addPerson);
        }

        $entityManager->flush();

        if (!empty($message)) {
            $httpCode = 400;
        } else {
            $httpCode = 200;
            $message[] = 'Le group a bien été ajouté';
        }

        $response = new JsonResponse($message, $httpCode);       
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

        } elseif ($userWedding->getId() != $guestGroup->getWedding()->getId()) {
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
    public function deleteGroup(GuestGroupRepository $guestGroupRepository, Request $request, UserRepository $userRepository)
    {
        //je récupère les données du front dans l'objet request.
        $content = $request->getContent();
        $contentDecode = json_decode($content);

        // récupération du wedding correspondant au user grâce à AuthenticatedListener
        $userWedding = $userRepository->findOneBy(['email' => $request->attributes->get('userEmail')])->getWedding();
        
        //je récupère le guestGroup 
        // $guestGroup = $guestGroupRepository->find($contentDecode->id);   //groupId modifié en id
        $guestGroup = $guestGroupRepository->findOneBy(['id' => $contentDecode->id]);
        
        if (!$guestGroup){
            $message = 'Le guestGroupId n\'existe pas';

            $response = new JsonResponse($message, 400);
            return $response;

        } elseif ($userWedding->getId() != $guestGroup->getWedding()->getId()) {
            $message = 'Le guestGroupId donné n\'est pas relié au wedding du user connecté';

            $response = new JsonResponse($message, 400);
        
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
     * @Route("/brides/group/mail", name="index_mail", methods={"GET","POST"})
     */
    public function indexMail(Request $request, GuestGroupRepository $guestGroupRepository, PersonRepository $personRepository, WeddingRepository $weddingRepository, UserRepository $userRepository)
    {
        // récupération du wedding correspondant au user grâce à AuthenticatedListener
        $userWedding = $userRepository->findOneBy(['email' => $request->attributes->get('userEmail')])->getWedding();

        $wedding = $weddingRepository->find($userWedding);
        
        if (!$wedding){
            $message = 'Le wedding id n\'existe pas.';
            
            $response = new JsonResponse($message, 400);
        
            return $response;
        }
        
        // $contactsGroup = $guestGroupRepository->findGroupsQueryBuilder($id);
        $groups = $guestGroupRepository->findGroupAndContactPerson($userWedding);

        //mariés exclus de ces comptes
        $countTotalGuests = $personRepository->findTotalGuestsCountQueryBuilder($userWedding);
        $countPresent = $personRepository->findAttendancePresentCountQueryBuilder($userWedding);
        $countAbsent = $personRepository->findAttendanceAbsentCountQueryBuilder($userWedding);
        //problème sur la requête, les autres fonctionnent bien avec le param id du wedding, mais celle-ci renvoie un count incorrect...
        $countWaiting = $personRepository->findAttendanceWaitingCountQueryBuilder($userWedding);

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
        $eventsForThisGroup = $eventRepository->findEventsActiveByGroup($slugUrl);
        
        //si besoin de tout avoir au même niveau sauf les people
        // $arrayResult = array_merge($guestGroupForWebsite, $newlyweds, $eventsForThisGroup);

        if (!$guestGroupForWebsite){
            $message = 'Le guestGroupId n\'existe pas';

            $response = new JsonResponse($message, 400);
        
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

    /**
     * @Route("/guests/website/form/{slugUrl}", name="website_form", requirements={"id"="\d+"}, methods={"POST"})
     */
    public function websiteForm(UserRepository $userRepository, PersonRepository $personRepository, Request $request, EntityManagerInterface $em, $slugUrl)
    {
        //je récupère les données du front dans l'objet request.
        $content = $request->getContent();
        $contentDecode = json_decode($content);

        foreach ($contentDecode->attendanceList as $attendee){
            $person = $personRepository->find($attendee);
            if (!$person){
                $message = 'L\'id de la personne n\'existe pas';
                $response = new JsonResponse($message, 400);
        
                return $response;
            }else {
                if ($person->getGuestGroup()->getSlugUrl() != $slugUrl){
                    // dd($person->getGuestGroup()->getSlugUrl(), $slugUrl);
                    $message = 'L\'id de la personne ne correspond pas au groupe';
                    $response = new JsonResponse($message, 400);
            
                    return $response;
                }
                $person->setAttendance(1);
                $em->persist($person);
                // dump($person);
            }
        }

        foreach ($contentDecode->unattendanceList as $absent){
            // dump($absent);
            $person = $personRepository->find($absent);
            
            if (!$person){
                $message = 'L\'id de la personne n\'existe pas';
                $response = new JsonResponse($message, 400);
        
                return $response;
            }else {
                if ($person->getGuestGroup()->getSlugUrl() != $slugUrl){
                    // dd($person->getGuestGroup()->getSlugUrl(), $slugUrl);
                    $message = 'L\'id de la personne ne correspond pas au groupe';
                    $response = new JsonResponse($message, 400);
            
                    return $response;
                }
                $person->setAttendance(2);
                $em->persist($person);
                // dump($person);
            }
            
        }
        // dd($contentDecode->attendance);
        $em->flush();
        
        $message = 'Les modification ont bien été prise en compte';

        $response = new JsonResponse($message, 200);       
        return $response;
    }
}
