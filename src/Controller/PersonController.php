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
            return $this->json(
                [
                    'code' => 404,
                    'message' => 'Le wedding id n\'existe pas',
                    'errors' => [],
                    'data' => [
                    ],
                    //'token' => 'youpi',
                    //'userid' => 'youpi',
                ]
            );
        }
        
        return $this->json(
            [
                'code' => 200,
                'message' => 'youpi',
                'errors' => [],
                'data' => [
                    'guests' => $guests,
                    'countTotalGuests' => $countTotalGuests,
                    'countPresent' => $countPresent,
                    'countAbsent' => $countAbsent,
                    'countWaiting' => $countWaiting
                ],
                //'token' => 'youpi',
                //'userid' => 'youpi',
            ]
        );
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
        
        if (!$wedding){
            return $this->json(
                [
                    'code' => 404,
                    'message' => 'Le wedding id n\'existe pas',
                    'errors' => [],
                    'data' => [
                    ],
                    //'token' => 'youpi',
                    //'userid' => 'youpi',
                ]
            );
        }

        $person = new Person();
        $person->setLastname($contentDecode->lastname);
        $person->setFirstname($contentDecode->firstname);
        $person->setWedding($wedding);
        $person->setNewlyweds(0);

        $guestGroup = new GuestGroup();
        $guestGroup->setWedding($wedding);

        //j'assigne les events au groupe
        // foreach
        // dd($contentDecode->events);
        // $events = ;
        // $guestGroup->addEvent($events);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($person);
        $guestGroup->setContactPerson($person);

        if ($contentDecode->email){
            $guestGroup->setEmail($contentDecode->email);
        };

        $entityManager->persist($guestGroup);

        $person->setGuestGroup($guestGroup);
        $entityManager->persist($person);

        //j'ajoute 0 ou plusieurs accompagnants
        $i = 1;
        $addPerson = '$addPerson'.$i;
       
        while ($i){
            $addPersonFirstname = 'addPersonFirstname'.$i;
            $addPersonLastname = 'addPersonLastname'.$i;
            if (isset($contentDecode->{$addPersonLastname})){
                $addPerson = new Person();
                $addPerson->setLastname($contentDecode->{$addPersonLastname});
                $addPerson->setFirstname($contentDecode->{$addPersonFirstname});
                $addPerson->setWedding($wedding);
                $addPerson->setNewlyweds(0);
                $addPerson->setGuestGroup($guestGroup);

                $entityManager->persist($addPerson);
                $i++;
            } else { 
                break;    
            }
        }
        
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

    /**
     * @Route("/brides/guests/edit/{id}", name="edit", requirements={"id"="\d+"}, methods={"GET", "POST"})
     */
    public function edit(GuestGroupRepository $guestGroupRepository, PersonRepository $personRepository, $id)
    {
        $guestGroup = $personRepository->findByGuestGroup($id);

        dd($guestGroup);
        
        if (!$guestGroup){
            return $this->json(
                [
                    'code' => 404,
                    'message' => 'Le guestGroupId n\'existe pas',
                    'errors' => [],
                    'data' => [
                    ],
                    //'token' => 'youpi',
                    //'userid' => 'youpi',
                ]
            );
        }

        //je récupère les données du front dans l'objet request.
        $content = $request->getContent();
        $contentDecode = json_decode($content);

        
        
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
