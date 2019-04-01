<?php

namespace App\Controller;

use DateTime;
use App\Repository\UserRepository;
use App\Repository\EventRepository;
use App\Repository\PersonRepository;
use App\Repository\WeddingRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class WeddingController extends AbstractController
{
    /**
     * @Route("/brides/mywedding/index", name="index", methods={"GET", "POST"})
     */
    public function index(PersonRepository $personRepository, Request $request, EventRepository $eventRepository, WeddingRepository $weddingRepository, UserRepository $userRepository)
    { 
        //je récupère les données du front dans l'objet request.
        $content = $request->getContent();
        $contentDecode = json_decode($content);

        // récupération du wedding correspondant au user grâce à AuthenticatedListener
        $userWedding = $userRepository->findOneBy(['email' => $request->attributes->get('userEmail')])->getWedding();

        $eventsWedding = $eventRepository->findThisWedding($userWedding);
        // $newlyweds = $personRepository->findByNewlyweds($id);
        // $thisWedding = $weddingRepository->findThisWedding($id);
                
        $data = 
            [
             'events' => $eventsWedding,
            //  'newlyweds' => $newlyweds,
            //  'wedding' => $thisWedding   
            ]
        ;
        $response = new JsonResponse($data, 200);
       
        return $response;
    }

    /**
     * @Route("/brides/mywedding/edit", name="edit", methods={"POST"})
     */
    public function edit(PersonRepository $personRepository, Request $request, EventRepository $eventRepository, WeddingRepository $weddingRepository, UserRepository $userRepository)
    { 
        //je récupère les données du front dans l'objet request.
        $content = $request->getContent();
        $contentDecode = json_decode($content);

        // récupération du wedding correspondant au user grâce à AuthenticatedListener
        $userWedding = $userRepository->findOneBy(['email' => $request->attributes->get('userEmail')])->getWedding();

        $eventsWedding = $eventRepository->findThisWedding($userWedding);
        
        
        foreach ($contentDecode->events as $oneEventDecode){
            
            $weddingEvent = $eventRepository->find($oneEventDecode->id);
            if (!$weddingEvent){
                $message = 'Il n\'y a pas d\'event avec l\'id correspondant.';
                
                $response = new JsonResponse($message, 400);
           
                return $response;
            } elseif ($userWedding != $weddingEvent->getWedding()) {
                $message = 'L\'événement ne correspond pas au bon mariage.';
                
                $response = new JsonResponse($message, 400);
           
                return $response;
            } else {
                if ($oneEventDecode->address){
                    $weddingEvent->setAddress($oneEventDecode->address);
                }
                if ($oneEventDecode->postcode){
                    $weddingEvent->setPostcode($oneEventDecode->postcode);
                }
                if ($oneEventDecode->city){
                    $weddingEvent->setCity($oneEventDecode->city);
                }
                //    dd($oneEventDecode->schedule);
                if (isset($oneEventDecode->schedule->date)){
                
                    $weddingEvent->setSchedule(\DateTime::createFromFormat('Y-m-d', $oneEventDecode->schedule->date));
                } else {
                    $weddingEvent->setSchedule(\DateTime::createFromFormat('Y-m-d', $oneEventDecode->schedule));
                }

               
                // dd($oneEventDecode->schedule->date->format('Y-m-d'));
                if ($oneEventDecode->hour){
                    $weddingEvent->setHour($oneEventDecode->hour);
                }
                if ($oneEventDecode->informations){
                    $weddingEvent->setInformations($oneEventDecode->informations);
                }
                if ($oneEventDecode->active){
                    $weddingEvent->setActive($oneEventDecode->active);
                }
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($weddingEvent);
                $entityManager->flush();
            }
        }         

        $eventsWedding = $eventRepository->findThisWedding($userWedding);
        
        $data = 
            [
            'events' => $eventsWedding,
            ]
        ;
        $response = new JsonResponse($data, 200);
       
        return $response;
    }
}
