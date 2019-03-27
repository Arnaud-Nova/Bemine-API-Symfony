<?php

namespace App\Controller;

use App\Repository\EventRepository;
use App\Repository\PersonRepository;
use App\Repository\WeddingRepository;
use App\Repository\WeddingEventRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class WeddingController extends AbstractController
{
    /**
     * @Route("/brides/mywedding/index/wedding/{id}", name="index", requirements={"id"="\d+"}, methods={"GET", "POST"})
     */
    public function index(PersonRepository $personRepository, $id, WeddingEventRepository $weddingEventRepository, Request $request, EventRepository $eventRepository)
    { 
        //je récupère les données du front dans l'objet request.
        $content = $request->getContent();
        $contentDecode = json_decode($content);
        $eventsWedding = $weddingEventRepository->findThisWedding($id);
        // $eventsType = $eventRepository->findAllEvents();
        
        $newlyweds = $personRepository->findByNewlyweds($id);
        
        //j'injecte le nom des events dans le tableau
        $allEvents = [];
        foreach ($eventsWedding as $event){
            // $event
            switch ($event['event_id']){
                case 1:
                $event1 = $eventRepository->find(1);
                // dd($event1);
                array_push($event, $event['event_name'] = $event1->getName());
                break;
                case 2:
                $event2 = $eventRepository->find(2);
                array_push($event, $event['event_name'] = $event2->getName());
                break;
                case 3:
                $event3 = $eventRepository->find(3);
                array_push($event, $event['event_name'] = $event3->getName());
                break;
                case 4:
                $event4 = $eventRepository->find(4);
                array_push($event, $event['event_name'] = $event4->getName());
                break;
            }
            array_push($allEvents, $event);
            // dump($event);
        }
        
        
       
        // dd($contentDecode);
        
        // dd($newlyweds);
        // foreach ($newlyweds as $newlywed){
        //     $newlywedOne = $personRepository->find($newlywed['id']);
        //     dd($newlywedOne);
        //     // dd($newlywedOne->getFirstname());
        //     foreach ($contentDecode->newlyweds as $decodeNewlywed){
        //         // dump($decodeNewlywed);
        //         $newlywedOne->setFirstname($decodeNewlywed->firstname);
        //         $newlywedOne->setLastname($decodeNewlywed->lastname);
        //         // $thisWedding->setDate($contentDecode->date);
        //     }
                
            
        //     $entityManager = $this->getDoctrine()->getManager();
        //     $entityManager->persist($newlywedOne);
        // }
        // $entityManager->flush();
        $data = 
            [
            //  'eventsType' => $eventsType,
             'events' => $allEvents,
             'newlyweds' => $newlyweds   
            ]
        ;
        $response = new JsonResponse($data, 200);
       
        return $response;
    }

    /**
     * @Route("/brides/mywedding/edit/wedding/{id}", name="edit", requirements={"id"="\d+"}, methods={"POST"})
     */
    public function edit(PersonRepository $personRepository, $id, WeddingEventRepository $weddingEventRepository, Request $request, EventRepository $eventRepository)
    { 
        //je récupère les données du front dans l'objet request.
        $content = $request->getContent();
        $contentDecode = json_decode($content);

        $eventsWedding = $weddingEventRepository->findThisWedding($id);
        // $eventsType = $eventRepository->findAllEvents();
        
        // dd($contentDecode->events);
        
        foreach ($contentDecode->events as $oneEventDecode){
            $weddingEvent = $weddingEventRepository->find($oneEventDecode->id);
            // $weddingEvent->setDate();
            $weddingEvent->setAddress($oneEventDecode->address);
            $weddingEvent->setPostcode($oneEventDecode->postcode);
            $weddingEvent->setCity($oneEventDecode->city);
            $weddingEvent->setSchedule($oneEventDecode->schedule);
            $weddingEvent->setInformations($oneEventDecode->informations);
            $weddingEvent->setActive($oneEventDecode->active);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($weddingEvent);
        }
        $entityManager->flush();
        
        // dd($contentDecode);
        
        
        // dd($newlyweds);
        // foreach ($newlyweds as $newlywed){
        //     $newlywedOne = $personRepository->find($newlywed['id']);
        //     dd($newlywedOne);
        //     // dd($newlywedOne->getFirstname());
        //     foreach ($contentDecode->newlyweds as $decodeNewlywed){
        //         // dump($decodeNewlywed);
        //         $newlywedOne->setFirstname($decodeNewlywed->firstname);
        //         $newlywedOne->setLastname($decodeNewlywed->lastname);
        //         // $thisWedding->setDate($contentDecode->date);
        //     }
                
            
        
        $data = 
            [
            //  'eventsType' => $eventsType,
             'events' => $allEvents,
             'newlyweds' => $newlyweds   
            ]
        ;
        $response = new JsonResponse($data, 200);
       
        return $response;
    }
}
