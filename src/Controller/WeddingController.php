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

    // /**
    //  * @Route("/brides/mywedding/index/wedding/{id}", name="index", requirements={"id"="\d+"}, methods={"GET", "POST"})
    //  */
    // public function index(PersonRepository $personRepository, $id, Request $request, EventRepository $eventRepository, WeddingRepository $weddingRepository, UserRepository $userRepository)
    // { 
    //     //je récupère les données du front dans l'objet request.
    //     $content = $request->getContent();
    //     $contentDecode = json_decode($content);
    //     $eventsWedding = $eventRepository->findThisWedding($id);
    //     // $newlyweds = $personRepository->findByNewlyweds($id);
    //     // $thisWedding = $weddingRepository->findThisWedding($id);
        
    //     $data = 
    //         [
    //          'events' => $eventsWedding,
    //         //  'newlyweds' => $newlyweds,
    //         //  'wedding' => $thisWedding   
    //         ]
    //     ;
    //     $response = new JsonResponse($data, 200);
       
    //     return $response;
    // }

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
                if ($oneEventDecode->schedule){
                    $weddingEvent->setSchedule(\DateTime::createFromFormat('Y-m-d H:i:s', $oneEventDecode->schedule));
                }
                if ($oneEventDecode->informations){
                    $weddingEvent->setInformations($oneEventDecode->informations);
                }
                $weddingEvent->setActive($oneEventDecode->active);
        
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($weddingEvent);
                $entityManager->flush();

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

        

       
    }

    //  /**
    //  * @Route("/brides/mywedding/edit/wedding/{id}", name="edit", requirements={"id"="\d+"}, methods={"POST"})
    //  */
    // public function edit(PersonRepository $personRepository, $id, Request $request, EventRepository $eventRepository, WeddingRepository $weddingRepository)
    // { 
    //     //je récupère les données du front dans l'objet request.
    //     $content = $request->getContent();
    //     $contentDecode = json_decode($content);

    //     $eventsWedding = $eventRepository->findThisWedding($id);
        
    //     foreach ($contentDecode->events as $oneEventDecode){
    //         $weddingEvent = $eventRepository->find($oneEventDecode->id);

    //         if ($oneEventDecode->address){
    //             $weddingEvent->setAddress($oneEventDecode->address);
    //         }
    //         if ($oneEventDecode->postcode){
    //             $weddingEvent->setPostcode($oneEventDecode->postcode);
    //         }
    //         if ($oneEventDecode->city){
    //             $weddingEvent->setCity($oneEventDecode->city);
    //         }
    //         if ($oneEventDecode->schedule){
    //             $weddingEvent->setSchedule(\DateTime::createFromFormat('Y-m-d H:i:s', $oneEventDecode->schedule));
    //         }
    //         if ($oneEventDecode->informations){
    //             $weddingEvent->setInformations($oneEventDecode->informations);
    //         }
    //         $weddingEvent->setActive($oneEventDecode->active);
    
    //         $entityManager = $this->getDoctrine()->getManager();
    //         $entityManager->persist($weddingEvent);
    //     }

    //     // foreach ($contentDecode->newlyweds as $newlywedDecode){
    //     //     $newlywed = $personRepository->find($newlywedDecode->id);
    //     //     $newlywed->setFirstname($newlywedDecode->firstname);
    //     //     $newlywed->setLastname($newlywedDecode->lastname);
    //     // }

    //     // //j'enregistre la nouvelle date en BDD
    //     // $thisWedding = $weddingRepository->find($id);
    //     // foreach($contentDecode->wedding as $oneWedding){
    //     //     $weddingDate = $oneWedding->date->date;
    //     //     $createDate = new DateTime($weddingDate);
    //     //     $finalDate = $createDate->format('Y-m-d');
    //     //     // dd($finalDate);
    //     //     $thisWedding->setDate(\DateTime::createFromFormat('Y-m-d', $finalDate));
    //     //     $entityManager->persist($thisWedding);

    //     // }   
       
        
    //     $entityManager->flush();

    //     $eventsWedding = $eventRepository->findThisWedding($id);
    //     // $newlyweds = $personRepository->findByNewlyweds($id);
    //     // $thisWeddingArray = $weddingRepository->findThisWedding($id);
        
    //     $data = 
    //         [
    //          'events' => $eventsWedding,
    //         //  'newlyweds' => $newlyweds,
    //         //  'wedding' => $thisWeddingArray   
    //         ]
    //     ;
    //     $response = new JsonResponse($data, 200);
       
    //     return $response;
    // }
}
